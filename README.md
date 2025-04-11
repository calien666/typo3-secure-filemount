[![Latest Stable Version](https://poser.pugx.org/calien/secure-filemount/v/stable.svg?style=for-the-badge)](https://packagist.org/packages/calien/secure-filemount)
[![TYPO3 12.4](https://img.shields.io/badge/TYPO3-12.4-green.svg?style=for-the-badge)](https://get.typo3.org/version/12)
[![TYPO3 13.4](https://img.shields.io/badge/TYPO3-13.3-green.svg?style=for-the-badge)](https://get.typo3.org/version/13)
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
