<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Calien\SecureFilemount\Service\AccessService;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\Stream;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;

class SecureFilemountMiddleware implements MiddlewareInterface
{
    /**
     * @var array|string[]
     */
    protected array $mimeTypeMappings = [
        'js' => 'text/javascript',
        'css' => 'text/css',
    ];

    /**
     * @inheritDoc
     * @throws AspectNotFoundException
     * @throws PageNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $storages = AccessService::getProtectedFileStorages();
        /** @var SiteRouteResult $path */
        $path = $request->getAttribute('routing');
        /** @var ResourceStorage $foundStorage */
        $foundStorage = null;
        $pathArray = array_filter(GeneralUtility::trimExplode('/', trim($path->getUri()->getPath(), '/')));
        $searchBasePath = array_shift($pathArray);
        foreach ($storages as $storage) {
            $conf = $storage->getConfiguration();
            $basePath = trim($conf['basePath'], '/');
            if ($searchBasePath === $basePath) {
                $foundStorage = $storage;
            }
        }
        if (!is_null($foundStorage)) {
            $accessGranted = AccessService::checkStorageAccess($foundStorage);
            if (!$accessGranted) {
                return GeneralUtility::makeInstance(ErrorController::class)->accessDeniedAction(
                    $request,
                    'You are not allowed to enter this content.'
                );
            }

            $identifier = substr($path->getUri()->getPath(), strlen($foundStorage->getConfiguration()['basePath']));
            $file = $foundStorage->getFile($identifier);
            $stream = new Stream($file->getForLocalProcessing());
            $mime = $file->getMimeType();
            if (array_key_exists($file->getExtension(), $this->mimeTypeMappings)) {
                $mime = $this->mimeTypeMappings[$file->getExtension()];
            }
            return (new Response())
                ->withHeader('Content-Type', $mime)
                ->withBody($stream);
        }
        return $handler->handle($request);
    }
}
