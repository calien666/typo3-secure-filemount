<?php

declare(strict_types=1);

namespace Calien\SecureFilemount\Tests\Functional\Middleware;

use Calien\SecureFilemount\Middleware\SecureFilemountMiddleware;
use Calien\SecureFilemount\Tests\Functional\AbstractFolderAccessTestCase;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\Site\Entity\NullSite;

/**
 * End-to-end wiring proof for the middleware: baseUri matching, identifier extraction and the
 * grant/deny branches. The gate decision itself is covered exhaustively by FolderAccessServiceTest.
 */
final class SecureFilemountMiddlewareTest extends AbstractFolderAccessTestCase
{
    #[Test]
    public function protectedFileIsStreamedForAuthorisedFrontendUser(): void
    {
        $this->actAsFrontendUser(2, [1]);

        $response = $this->processRequest('/secure-dl/secure/restricted/doc.txt');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('restricted', (string)$response->getBody());
    }

    #[Test]
    public function protectedFileIsDeniedForAnonymousVisitor(): void
    {
        $this->actAsAnonymous();

        $response = $this->processRequest('/secure-dl/secure/restricted/doc.txt');

        self::assertSame(403, $response->getStatusCode());
    }

    #[Test]
    public function requestOutsideAnyProtectedStorageIsDelegatedToTheNextHandler(): void
    {
        $this->actAsAnonymous();

        $response = $this->processRequest('/some/public/page');

        self::assertSame(418, $response->getStatusCode());
    }

    private function processRequest(string $path): ResponseInterface
    {
        $uri = new Uri($path);
        $request = (new ServerRequest($uri))
            ->withAttribute('routing', new SiteRouteResult($uri, new NullSite()));

        $handler = new class () implements RequestHandlerInterface {
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return new Response('php://memory', 418);
            }
        };

        return $this->get(SecureFilemountMiddleware::class)->process($request, $handler);
    }
}
