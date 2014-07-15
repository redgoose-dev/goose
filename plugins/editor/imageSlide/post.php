<?php
if(!defined("GOOSE")){exit();}

$path = ROOT.'/plugins/editor/'.$nest['editor'];
$extPath = ROOT.'/libs/ext';
require('attachFileDatas.php');
?>

<link rel="stylesheet" href="<?=$path?>/css/UploadInterface.css" />
<link rel="stylesheet" href="<?=$extPath?>/Jcrop/jquery.Jcrop.min.css" />

<input type="hidden" name="addQueue" value="" />
<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
<input type="hidden" name="thumnail_image" value="" />

<fieldset>
	<dl>
		<dt><label for="fileUpload">파일첨부</label></dt>
		<dd>
			<div class="box">
				<input type="file" name="fileUpload" id="fileUpload" multiple />
				<button type="button" id="fileUploadButton">업로드</button>
			</div>
		</dd>
	</dl>
	<div class="queuesManager" id="queuesManager"></div>
</fieldset>

<script>function log(o){console.log(o);}</script>
<script src="<?=$jQueryAddress?>"></script>
<script src="<?=$extPath?>/Jcrop/jquery.Jcrop.min.js"></script>
<script src="<?=$path?>/js/FilesQueue.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/FileUpload.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/Thumnail.class.js"></script>
<script src="<?=$path?>/js/UploadInterface.class.js"></script>
<script>
jQuery(function($){
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		form : document.writeForm
		,$queue : $('#queuesManager')
		,uploadAction : '<?=ROOT?>/files/upload/'
		,removeAction : '<?=ROOT?>/files/remove/'
		,fileDir : '<?=ROOT?>/data/original/'
		,auto : false
		,limit : 30
		,thumnailType : '<?=$nest['thumnailType']?>'
		,thumnailSize : '<?=$nest['thumnailSize']?>'
		,queueType : 'list' // list | gallery
		,queueForm : [
			{ label : 'Subject', name : 'subject', type : 'text', size : 15 }
			,{ label : 'Content', name : 'content', type : 'textarea', rows : 3 }
			,{ label : 'URL', name : 'url', type : 'text', size : 20 }
		]
	});

	var attachFiles = '<?=$pushData?>';
	var attachFilesData = (attachFiles) ? JSON.parse(attachFiles) : null;
	if (attachFilesData)
	{
		uploadInterface.pushQueue(attachFilesData);
		// importJSON 메서드를 만들어서 json으로된 첨부파일 목록을 가져와서 목록을 만든다.
	}

	// upload button click event
	$('#fileUploadButton').on('click', function(){
		uploadInterface.upload();
	});

	// onsubmit event
	$(document.writeForm).on('submit', function(){
		if (uploadInterface.thumnailImageCheck())
		{
			// 첨부파일 목록을 json으로 바꿔서 content로 집어넣기
			/*
			uploadInterface.exportJSON({
				target : $('#content')
			});
			*/
			return false;
		}
	});

	$('button[role-action=ExportJSON]').on('click', function(){
		log($(this));
	});
});
</script>



<hr />
<p><button type="button" role-action="ExportJSON" class="ui-button btn-small btn-highlight">Export JAON</button></p>
<p><textarea name="content" id="content" rows="10" class="block"><?=$article[content]?></textarea></p>
