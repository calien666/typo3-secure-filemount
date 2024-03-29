.. include:: /Includes.rst.txt

.. _installation:

Installation
============

The extension needs to be installed as any other extension of TYPO3 CMS. Get the
extension by one of the following methods:

#.  **Use composer**:
    Run

    .. code-block:: bash

        composer require calien/secure-filemount

    in your TYPO3 installation.

#.  **Get it from the Extension Manager**:
    Switch to the module :guilabel:`Admin Tools > Extensions`.
    Switch to :guilabel:`Get Extensions` and search for the extension key
    *secure_filemount* and import the extension from the repository.

#. **Get it from typo3.org**:
    You can always get current version from `TER`_ by downloading the zip
    version. Upload the file afterwards in the Extension Manager.

and :ref:`configure <extensionConfiguration>` it.

Inside your own site configuration follow the instructions for
:ref:`table configuration <tableConfiguration>`.

.. _TER: https://extensions.typo3.org/extension/secure_filemount

Compatibility
-------------

*secure_filemount* supports TYPO3 v11.
