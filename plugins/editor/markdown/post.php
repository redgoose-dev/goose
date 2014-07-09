<?php
if(!defined("GOOSE")){exit();}

$path = ROOT.'/plugins/editor/'.$nest['editor'];
require('lib/attachFileDatas.php');
?>

<link rel="stylesheet" href="<?=$path?>/css/upload.css" />
<link rel="stylesheet" href="<?=$path?>/lib/Jcrop/jquery.Jcrop.min.css" />

<input type="hidden" name="addQueue" value="" />
<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
<input type="hidden" name="thumnail_image" value="" />

<fieldset>
	<dl>
		<dt><label for="content">내용</label></dt>
		<dd>
			<textarea name="content" id="content" rows="20" class="block"><?=htmlspecialchars($article[content])?></textarea>
			<p>
				* 마크다운 한글 메뉴얼 : <a href="http://scriptogr.am/myevan/post/markdown-syntax-guide-for-scriptogram" target="_blank">바로가기</a><br/>
				* 존 그루버 마크다운 페이지 번역 : <a href="http://nolboo.github.io/blog/2013/09/07/john-gruber-markdown/" target="_blank">바로가기</a>
			</p>
		</dd>
	</dl>
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
<script src="<?=$path?>/lib/Jcrop/jquery.Jcrop.min.js"></script>
<script src="<?=$path?>/js/FilesQueue.class.js"></script>
<script src="<?=$path?>/js/FileUpload.class.js"></script>
<script src="<?=$path?>/js/Thumnail.class.js"></script>
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
		,limit : 3
		,thumnailType : '<?=$nest['thumnailType']?>'
		,thumnailSize : '<?=$nest['thumnailSize']?>'
		,$insertTarget : $('#content')
		,insertFunc : null // function(value){}
	});

	var attachFiles = '<?=$pushData?>';
	var attachFilesData = (attachFiles) ? JSON.parse(attachFiles) : null;
	if (attachFilesData)
	{
		uploadInterface.pushQueue(attachFilesData);
	}

	// upload button click event
	$('#fileUploadButton').on('click', function(){
		uploadInterface.upload();
	});

	// onsubmit event
	$(document.writeForm).on('submit', function(){
		if (uploadInterface.thumnailImageCheck())
		{
			return false;
		}
	});
});
</script>