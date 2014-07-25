<?php
if(!defined("GOOSE")){exit();}

$pushData = null;
$path = GOOSE_ROOT.'/plugins/editor/'.$nest['editor'];
$extPath = GOOSE_ROOT.'/libs/ext';

require('attachFileDatas.php');
?>

<link rel="stylesheet" href="<?=$path?>/css/UploadInterface.css" />
<link rel="stylesheet" href="<?=$extPath?>/Jcrop/jquery.Jcrop.min.css" />

<input type="hidden" name="addQueue" value="" />
<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
<input type="hidden" name="thumnail_image" value="" />
<input type="hidden" name="content" value="<?=$article['content']?>"/>

<fieldset>
	<dl>
		<dt><label for="fileUpload">파일첨부</label></dt>
		<dd>
			<div class="box">
				<input type="file" name="fileUpload" id="fileUpload" multiple />
			</div>
		</dd>
	</dl>
	<div class="queuesManager" id="queuesManager"></div>
</fieldset>

<script>function log(o){console.log(o);}</script>
<script src="<?=$jQueryAddress?>"></script>
<script src="<?=$extPath?>/Jcrop/jquery.Jcrop.min.js"></script>
<script src="<?=$extPath?>/dragsort/jquery.dragsort-0.5.1.min.js"></script>
<script src="<?=$path?>/js/FilesQueue.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/FileUpload.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/Thumnail.class.js"></script>
<script src="<?=$path?>/js/UploadInterface.class.js"></script>
<script>
jQuery(function($){
	var $form = $(document.writeForm);
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		form : $form.get(0)
		,$queue : $('#queuesManager')
		,uploadAction : '<?=GOOSE_ROOT?>/files/upload/'
		,removeAction : '<?=GOOSE_ROOT?>/files/remove/'
		,fileDir : '<?=GOOSE_ROOT?>/data/original/'
		,auto : true
		,limit : 30
		,thumnailType : '<?=$nest['thumnailType']?>'
		,thumnailSize : '<?=$nest['thumnailSize']?>'
		,queueForm : [
			{ label : 'Subject', name : 'subject', value : '' }
			,{ label : 'Content', name : 'content', value : '' }
		]
	});

	// push data
	var attachFiles = '<?=$pushData?>';
	attachFiles = (attachFiles) ? JSON.parse(attachFiles) : null;
	var contentData = $form.find('input[name=content]').val();
	contentData = (contentData) ? JSON.parse(decodeURIComponent(contentData)) : null;

	if (attachFiles)
	{
		if (contentData)
		{
			// srl값이 일치하지 않아 srl값 매칭
			for (var n=0; n<contentData.length; n++)
			{
				for (var nn=0; nn<attachFiles.length; nn++)
				{
					if (contentData[n].location == attachFiles[nn].location)
					{
						contentData[n].srl = attachFiles[nn].srl;
						break;
					}
				}
			}
			uploadInterface.pushQueue(contentData);
		}
		else
		{
			uploadInterface.pushQueue(attachFiles);
		}
	}

	// onsubmit event
	$(document.writeForm).on('submit', function(){
		var error = uploadInterface.thumnailImageCheck();
		if (error)
		{
			return false;
		}
		var json = uploadInterface.exportJSON();
		$form.find('input[name=content]').val(json);
	});
});
</script>
