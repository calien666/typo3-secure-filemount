.. include:: /Includes.rst.txt

.. _configuration:

Configuration
-------------

.. attention::

    To establish complete security, the secure file storage should be located
    outside the public directory. TYPO3 can work with file storages outside of
    its own system, so there are no problems to be editorially active in the
    backend. A file store outside the public directory increases the security of
    the system by design rather than by configuration.

Steps
=====

#.  create a local storage (best outside TYPO3 public)

    .. code-block:: bash

        mkdir -p ${TYPO3_PUBLIC_PATH}/../private/secure_fileadmin/

#.  Create a File Storage in backend

    .. figure:: /Images/Administration/filestorage-general.png
        :alt: Setup of a secure file storage

    Be aware of the Base URI, as this field needs to be set up.

#.  Optional: Set up an access group for this file storage. This Access group is
    working as fallback, if no access is defined in file list module.

    .. figure:: /Images/Administration/filestorage-access.png
        :alt: Defined access group in file storage record, tab *access*

.. note::

    According to your web server, there should be settings done to redirect
    the access to files to the TYPO3 instead of answering with a 404 - Not found

Server Configuration
====================

nginx Web Server
^^^^^^^^^^^^^^^^

.. code-block:: nginx
    :caption: nginx example configuration
    :linenos:
    :emphasize-lines: 3

    location ~* \.(?:jpg|jpeg|gif|png|ico|cur|gz|svg|svgz|mp4|ogg|ogv|webm|htc)$ {
        # your configuration here
        try_files $uri /index.php?$uri;
    }

Apache2 Web Server
^^^^^^^^^^^^^^^^^^

.. code-block:: apache
    :caption: Apache2 example configuration

    RewriteCond %{DOCUMENT_ROOT}/%{REQUEST_FILENAME} !-f
    RewriteRule ^/(.*)$ /index.php?%{REQUEST_URI} [P,QSA,L]
