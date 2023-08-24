/**
 * Module: TYPO3/CMS/SecureFilemount/ContextMenuActions
 *
 * JavaScript to handle the click action of the "SecureFilemount" context menu item
 * @exports TYPO3/CMS/SecureFilemount/ContextMenuActions
 */
define(function () {
  'use strict';

  /**
   * @exports TYPO3/CMS/SecureFilemount/ContextMenuActions
   */
  var ContextMenuActions = {};

  /**
   * Open folder permissions edit form
   *
   * @param {string} table
   * @param {string} uid combined folder identifier
   */
  ContextMenuActions.editRecord = function (table, uid) {
    var folderRecordUid = this.data('folderRecordUid') || 0;

    if (folderRecordUid > 0) {
      top.TYPO3.Backend.ContentContainer.setUrl(
        top.TYPO3.settings.FormEngine.moduleUrl
        + '&edit[tx_securefilemount_folder][' + parseInt(folderRecordUid, 10) + ']=edit'
        + '&returnUrl=' + ContextMenuActions.getReturnUrl()
      );
    } else {
      top.TYPO3.Backend.ContentContainer.setUrl(
        top.TYPO3.settings.FormEngine.moduleUrl
        + '&edit[tx_securefilemount_folder][0]=new'
        + '&defVals[tx_securefilemount_folder][storage]=' + this.data('storage')
        + '&defVals[tx_securefilemount_folder][folder]=' + this.data('folder')
        + '&defVals[tx_securefilemount_folder][folder_hash]=' + this.data('folderHash')
        + '&returnUrl=' + ContextMenuActions.getReturnUrl()
      );
    }
  };

  ContextMenuActions.getReturnUrl = function () {
    return encodeURIComponent(top.list_frame.document.location.pathname + top.list_frame.document.location.search);
  };

  return ContextMenuActions;
});
