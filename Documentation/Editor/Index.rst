.. include:: /Includes.rst.txt

.. _editor:

Working with secure file storages
=================================

After the file storages have been configured, the editor can work normally in
the File List module.

To secure individual folders, the editor has two options:

#.  in the folder tree by right-clicking on the folder -> Edit Access

    .. figure:: /Images/Editor/edit-access-folder-tree.png
        :alt: Screenshot of tree with active context menu, last point is "Edit access rights"

#.  in the module itself via the Edit access button

    .. figure:: /Images/Editor/edit-access-module.png
        :alt: Screenshot of module window, "Edit access rights" button hovered

To enable access restrictions only add the frontend user groups, you want to
give access. This is default TYPO3 behaviour.

.. figure:: /Images/Editor/edit-access-editing-rights.png
    :alt: Screenshot of editing access record

After enabling access to specific groups or "Show at any login", the tree shows
the set up access to the folder.

.. figure:: /Images/Editor/access-tree-view.png
    :alt: Tree view of folders with access given. Small Icon appears on restricted folders

Now you are able to use files as you are working with standard fileadmin. Just
include them in your content elements and so on. This extension cares about the
correct access and answers with 403 - Auth required, if no access is given.

Feel free to add an 403 error handler to your site configuration to handle access.

.. note::

    When using this extension in combination with [EXT:Solr](https://docs.typo3.org/p/apache-solr-for-typo3/solr/main/en-us/)
    and any file indexing extension indexed files automatically receive their respective access rights
    and will only be shown in the search results with a valid/the correct login.
