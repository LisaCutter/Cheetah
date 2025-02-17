/**
 * This work, "Cheetah - https://www.cheetahwsb.com", is a derivative of "Dolphin Pro V7.4.2" by BoonEx Pty Limited - https://www.boonex.com/, used under CC-BY. "Cheetah" is licensed under CC-BY by Dean J. Bassett Jr.
 * CC-BY License - http://creativecommons.org/licenses/by/3.0/
 */

var bPossibleToReload = false;
var bMusicRecorded = false;

function rayMusicReady(bMode, sExtra) {
	bMusicRecorded = bMode;
	shMusicEnableSubmit(bMode);
	if(!bMusicRecorded)
		$('#sound_accepted_files_block').text("");
}

function shMusicEnableSubmit(bMode) {
	if($('#sound_upload_form').attr("name") == 'embed')
		return;

	var oButton = $('#sound_upload_form .form_input_submit');
	if(bMode)
		oButton.removeAttr('disabled');
	else
		oButton.attr('disabled', 'disabled');
}

function ChSoundUpload(oOptions) {
    //this._sActionsUrl = oOptions.sActionUrl;
    //this._sObjName = oOptions.sObjName == undefined ? 'oCommonUpload' : oOptions.sObjName;
    this._iOwnerId = oOptions.iOwnerId == undefined ? 0 : parseInt(oOptions.iOwnerId);
    //this._iGlobAllowHtml = 0;
    // this._sAnimationEffect = oOptions.sAnimationEffect == undefined ? 'slide' : oOptions.sAnimationEffect;
    // this._iAnimationSpeed = oOptions.iAnimationSpeed == undefined ? 'slow' : oOptions.iAnimationSpeed;
}

ChSoundUpload.prototype.genSendFileInfoForm = function(iMID, sForm) {
    if (iMID > 0 && sForm != '') {
    	$(sForm).appendTo('#sound_accepted_files_block').addWebForms();
    	this.changeContinueButtonStatus();
    }
}

ChSoundUpload.prototype.getType = function() {
	return $('#sound_upload_form').attr("name");
}

ChSoundUpload.prototype.changeContinueButtonStatus = function () {
	switch(this.getType()) {
		case 'upload':
			var sFileVal = $('#sound_upload_form .sound_upload_form_wrapper .form_input_file').val();
			var sAcceptedFilesBlockVal = $('#sound_accepted_files_block').text();
			shMusicEnableSubmit(sFileVal != null && sFileVal != '' && sAcceptedFilesBlockVal == '');

            bPossibleToReload = true;
			break;

		case 'embed':
			this.checkEmbed();

            bPossibleToReload = true;
			break;

		case 'record':
			shMusicEnableSubmit(bMusicRecorded && $('#sound_accepted_files_block').text() == "");

            bPossibleToReload = true;
			break;

		default:
			break;
	}
}

ChSoundUpload.prototype.doValidateFileInfo = function(oButtonDom, iFileID) {
	var bRes = true;
	if ($('#send_file_info_' + iFileID + ' input[name=title]').val()=='') {
		$('#send_file_info_' + iFileID + ' input[name=title]').parent().parent().children('.warn').show().attr('float_info', _t('_ch_sounds_val_title_err'));
		bRes = false;
	}
	else
		$('#send_file_info_' + iFileID + ' input[name=title]').parent().parent().children('.warn').hide();

        return bRes; //can submit
}

ChSoundUpload.prototype.cancelSendFileInfo = function(iMID, sWorkingFile) {
	if(iMID == "")
		this.cancelSendFileInfoResult("");
    else if(iMID > 0 && sWorkingFile == "")
		this.cancelSendFileInfoResult(iMID);
	else
	{
		var $this = this;
		$.post(ch_append_url_params(sWorkingFile, "action=cancel_file&file_id=" + iMID), function(data){
			if (data==1)
				$this.cancelSendFileInfoResult(iMID);
		});
	}
}

ChSoundUpload.prototype.cancelSendFileInfoResult = function(iMID) {
	$('#send_file_info_'+iMID).remove();
	this.changeContinueButtonStatus();

    $('#sound_accepted_files_block script').remove();
    if (bPossibleToReload && $('#sound_accepted_files_block').text() == '')
        window.location.href = window.location.href;
}

ChSoundUpload.prototype.onSuccessSendingFileInfo = function(iMID) {
	$('#send_file_info_'+iMID).remove();

	setTimeout( function(){
		$('#sound_success_message').show(1000)
		setTimeout( function(){
			$('#sound_success_message').hide(1000);
		}, 3000);
	}, 500);

	this.changeContinueButtonStatus();

    $('#sound_accepted_files_block script').remove();
    if (bPossibleToReload && $('#sound_accepted_files_block').text() == '')
        window.location.href = window.location.href;

	switch(this.getType()) {
		case 'upload':
			this.resetUpload();
			break
		case 'embed':
			this.resetEmbed();
			break;
		case 'record':
			getRayFlashObject("mp3", "recorder").removeRecord();
			break;
	}
}

ChSoundUpload.prototype.showErrorMsg = function(sErrorCode) {
	var oErrorDiv = $('#' + sErrorCode);

	var $this = this;

	setTimeout( function(){
		oErrorDiv.show(1000)
		setTimeout( function(){
			oErrorDiv.hide(1000);
			$this._loading(false);
		}, 3000);
	}, 500);

}

ChSoundUpload.prototype.onFileChangedEvent = function (oElement) {
	this.changeContinueButtonStatus();
};

ChSoundUpload.prototype._loading = function (bShow) {
	$('.upload-loading-container').ch_loading(bShow);
}

ChSoundUpload.prototype.resetUpload = function () {
	var oCheck = $('#sound_upload_form [type="checkbox"]');
	oCheck.removeAttr("checked");

	var oFiles = $('#sound_upload_form .input_wrapper_file');
	var oFileIcons = $('#sound_upload_form .multiply_remove_button');
	if (oFiles.length>1) {
		oFiles.each( function(iInd) {
			if (iInd != 0) {
				$(this).remove();
			}
		});
		oFileIcons.each( function(iIndI) {
			$(this).remove();
		});
	}

	var oFile = $('#sound_upload_form [type="file"]');
	oFile.val("");

	shMusicEnableSubmit(false);
}

ChSoundUpload.prototype.resetEmbed = function () {
	var tText = $('#sound_upload_form [name="embed"]');
	tText.attr("value", "");
	shMusicEnableSubmit(false);
}

ChSoundUpload.prototype.checkEmbed = function (bAlert) {
	var tText = $('#sound_upload_form [name="embed"]');
	var sText = tText.attr("value").split(" ").join("");

	var bResult = /^https?:\/\/(www.)?youtube.com\/watch\?v=([0-9A-Za-z_]{11})$/.test(sText) && $('#sound_accepted_files_block').text() == "";
	if (bAlert && !bResult)
        alert(_t('_ch_sounds_emb_err'));
	return bResult;
}
