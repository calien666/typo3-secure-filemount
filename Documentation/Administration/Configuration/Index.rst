.. include:: /Includes.rst.txt

.. _configuration:

Configuration
-------------

.. attention::

    To establish complete security, the secure file storage must be located
    outside the public directory. TYPO3 can work with file storages outside of
    its own system, so there are no problems to be editorially active in the
    backend. A file store outside the public directory increases the security of
    the system by design rather than by configuration.

Steps
=====

#.  create a local storage (outside TYPO3 public)

    .. code-block:: bash

        mkdir -p ${TYPO3_PUBLIC_PATH}/../private/secure_fileadmin/

#.  Create a File Storage in backend

    .. figure:: /Images/Administration/filestorage-general.png
        :alt: Setup of a secure file storage

    Be aware of the Base URI, as this field needs to be set up. This ensures
    speaking URLs in the frontend and the middleware accessing the right
    storage.

#.  Optional: Set up an access group for this file storage. This Access group is
    working as fallback, if no access is defined in file list module.

    .. figure:: /Images/Administration/filestorage-access.png
        :alt: Defined access group in file storage record, tab *access*

.. note::

    According to your web server, there should be settings done to redirect
    the access to files to the TYPO3 instead of answering with a 404 - Not found

Backend Users and groups
========================

.. attention::

    Due to core restrictions on table access, it is highly needed to allow all
    backend users access to table `tx_securefilemount_folder`. Users will never
    see this table in a list, as the table is located on root level and hidden
    (like sys_file_metadata, sys_file).

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
