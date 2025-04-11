class ContextMenuActions {
  static getReturnUrl() {
    return encodeURIComponent(top.list_frame.document.location.pathname + top.list_frame.document.location.search);
  }

  static editRecord(table, uid, contextData) {
    var folderRecordUid = contextData.folderRecordUid || 0;

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
        + '&defVals[tx_securefilemount_folder][storage]=' + contextData.storage
        + '&defVals[tx_securefilemount_folder][folder]=' + contextData.folder
        + '&defVals[tx_securefilemount_folder][folder_hash]=' + contextData.folderHash
        + '&returnUrl=' + ContextMenuActions.getReturnUrl()
      );
    }
  }
}
export default ContextMenuActions;
