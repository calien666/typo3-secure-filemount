[![Latest Stable Version](https://poser.pugx.org/calien/secure-filemount/v/stable.svg?style=for-the-badge)](https://packagist.org/packages/calien/secure-filemount)
[![TYPO3 13.4](https://img.shields.io/badge/TYPO3-13.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/13)
[![TYPO3 14.3](https://img.shields.io/badge/TYPO3-14.3-green.svg?style=for-the-badge)](https://get.typo3.org/version/14)
[![License](http://poser.pugx.org/calien/secure-filemount/license?style=for-the-badge)](https://packagist.org/packages/calien/secure-filemount)
[![Total Downloads](https://poser.pugx.org/calien/secure-filemount/downloads.svg?style=for-the-badge)](https://packagist.org/packages/calien/secure-filemount)
[![Monthly Downloads](https://poser.pugx.org/calien/secure-filemount/d/monthly?style=for-the-badge)](https://packagist.org/packages/calien/secure-filemount)

# TYPO3 extension `secure_filemount`

This extension adds an access system for the file storage to TYPO3. By means of
this extension, private file stores are supplemented with finely granulated
access for frontend users.

## Features

* Add fine granulated access per directory
* webserver independent
  * works with nginx, Apache2, IIS and others
* Store files outside your public directory
* Fully accessible in backend
* Works with processed images
* speaking URL paths instead of eID call

## Installation

Install with your flavour:

* [TER](https://extensions.typo3.org/extension/secure_filemount/)
* Extension Manager
* composer

We prefer composer installation:
```bash
composer req calien/secure-filemount
```

## Configuration

Due to core restrictions on table access, it is highly needed to allow all
backend users access to table `tx_securefilemount_folder`. Users will never see
this table in a list, as the table is located on root level and hidden (like
sys_file_metadata, sys_file).

|                  | URL                                                          |
|------------------|--------------------------------------------------------------|
| **Repository:**  | https://github.com/calien666/typo3-secure-filemount          |
| **Read online:** | https://docs.typo3.org/p/calien/secure-filemount/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/secure_filemount/     |

## Create a release (maintainers only)

Prerequisites:

* git binary
* ssh key allowed to push new branches to the repository
* GitHub command line tool `gh` installed and configured with a user allowed
  to create and merge pull requests
* [`tailor`](https://github.com/TYPO3/tailor) available as `tailor`

> Set `RELEASE_BRANCH` to the branch the release happens on, for example
> `main`.
> Set `RELEASE_VERSION` to the version to release, for example `3.0.1`.
> Set `NEXT_DEV_VERSION` to the version development continues with, for
> example `3.0.2`.

```bash
echo '>> Create release' ; \
  RELEASE_BRANCH='main' ; \
  RELEASE_VERSION='3.0.1' ; \
  NEXT_DEV_VERSION='3.0.2' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  git checkout ${RELEASE_BRANCH} && \
  git pull --rebase && \
  git checkout -b release-${RELEASE_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${RELEASE_VERSION}\"/" Build/Scripts/runTests.sh && \
  tailor set-version ${RELEASE_VERSION} && \
  echo "${RELEASE_VERSION}" > VERSION && \
  git add -u && \
  git commit -m "[RELEASE] ${RELEASE_VERSION}" && \
  git push --set-upstream origin release-${RELEASE_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[RELEASE] ${RELEASE_VERSION}" && \
  gh pr view --web && \
  sleep 30 && \
  gh pr checks --watch --interval 2 && \
  sleep 5 && \
  gh pr merge -rd --admin && \
  git remote prune origin && \
  git tag ${RELEASE_VERSION} && \
  git push --tags && \
  git checkout -b set-dev-version-${NEXT_DEV_VERSION} && \
  tailor set-version ${NEXT_DEV_VERSION} && \
  echo "${NEXT_DEV_VERSION}-dev" > VERSION && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${NEXT_DEV_VERSION}-dev\"/" Build/Scripts/runTests.sh && \
  sed -i "s/^  RELEASE_VERSION=.*/  RELEASE_VERSION='${RELEASE_VERSION}' ; \\\/" README.md && \
  sed -i "s/^  NEXT_DEV_VERSION=.*/  NEXT_DEV_VERSION='${NEXT_DEV_VERSION}' ; \\\/" README.md && \
  git add -u && \
  git commit -m "[TASK] Set \"${NEXT_DEV_VERSION}-dev\"" && \
  git push --set-upstream origin set-dev-version-${NEXT_DEV_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[TASK] Set \"${NEXT_DEV_VERSION}-dev\"" && \
  gh pr view --web && \
  sleep 30 && \
  gh pr checks --watch --interval 2 && \
  sleep 5 && \
  gh pr merge -rd --admin && \
  git remote prune origin
```

Pushing the tag triggers the `publish` workflow. It verifies that the tag
matches `VERSION` and `ext_emconf.php`, creates the GitHub release and uploads
the release to the TYPO3 Extension Repository.
