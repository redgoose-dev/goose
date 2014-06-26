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
		<dt><label for="attachfiles">파일첨부</label></dt>
		<dd>
			<div id="filesUpload">
				<input type="file" name="attachfiles[]" id="attachfiles" multiple />
				<button type="button" role="upload">업로드</button>
			</div>
		</dd>
	</dl>
</fieldset>

<script src="<?=$jQueryAddress?>"></script>
<script src="<?=$path?>/js/jquery.form.js"></script>
<script src="<?=$path?>/js/fileUpload.js"></script>
<script>
jQuery(function($){
	var fileUpload = new FilesUpload(document.getElementById('filesUpload'), {
		aaa : 'apple'
	});
});
</script>
