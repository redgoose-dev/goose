<?php
if(!defined("GOOSE")){exit();}

$path = ROOT.'/plugins/editor/'.$nest['editor'];
?>

<link rel="stylesheet" href="<?=$path?>/css/post.css" />

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
			<input type="file" name="fileUpload" id="fileUpload" multiple />
			<button type="button" id="fileUploadButton">업로드</button>
		</dd>
	</dl>
	<article>
		<h1>파일첨부 관리</h1>
		<div class="filesQueue" id="filesQueue">
			<figure class="thumnail">
				.thumnail img
			</figure>
			<ul>
<!--
				<li>
					<div class="body">
						<span class="name">image_file_name.jpg</span>
						<span class="size">(235kb)</span>
						<span class="stat">- Ready</span>
					</div>
					<div class="progress">
						<p class="graph"><span></span></p>
					</div>
					<nav>
						<button type="button" rg-action="useThumnail">썸네일 이미지</button>
						<button type="button" rg-action="delete">삭제</button>
					</nav>
				</li>
-->
			</ul>
		</div>
		<nav id="queueController">
			<button type="button" rg-action="selectAll">모두선택</button>
			<button type="button" rg-action="insertContents">본문삽입</button>
			<button type="button" rg-action="deleteSelect">선택삭제</button>
			<button type="button" rg-action="deleteAll">모두삭제</button>
		</nav>
	</article>
</fieldset>

<script>function log(o){console.log(o);}</script>
<script src="<?=$jQueryAddress?>"></script>
<script src="<?=$path?>/js/FilesQueue.class.js"></script>
<script src="<?=$path?>/js/FileUpload.class.js"></script>
<script src="<?=$path?>/js/UploadInterface.class.js"></script>
<script>
jQuery(function($){
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		action : '<?=ROOT?>/files/upload/'
		,auto : true
		,$queue : $('#filesQueue')
		,$controller : $('#queueController')
		,limit : 3
		,token : '<?=md5("uPloAD_toKEn" . time());?>'
	});

	// upload button click event
	$('#fileUploadButton').on('click', function(){
		uploadInterface.upload();
	});
});
</script>