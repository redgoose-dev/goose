<?php
if(!defined("GOOSE")){exit();}

$path = '/plugins/editor/'.$nest['editor'];
$extPath = GOOSE_ROOT.'/libs/ext';
require('attachFileDatas.php');
$allTagsData = null;

if (file_exists(PWD.$path.'/tags.user.txt'))
{
	$allTagsData = $util->fop(PWD.$path.'/tags.user.txt', 'r');
}

// $article['json']
?>

<link rel="stylesheet" href="<?=$extPath?>/UploadInterface/UploadInterface.css" />
<link rel="stylesheet" href="<?=$extPath?>/TagManager/TagManager.css" />
<link rel="stylesheet" href="<?=$extPath?>/Jcrop/jquery.Jcrop.min.css" />

<input type="hidden" name="addQueue" value="" />
<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
<input type="hidden" name="thumnail_image" value="" />
<input type="hidden" name="json" />

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
	<dl class="table">
		<dt><label for="tag">태그</label></dt>
		<dd>
			<input type="text" name="tag" id="tag" />
			<button type="button" class="ui-button btn-small" role-action="addTag">추가</button>
			<?
			if ($allTagsData)
			{
				$allTagsData = json_decode($allTagsData);
			?>
				<div class="tagIndexWrap" id="allTagsIndex">
					<div class="list">
						<ul>
							<?
							foreach ($allTagsData as $k=>$v)
							{
								echo "<li><span>$v->name</span><em>$v->count</em></li>";
							}
							?>
						</ul>
					</div>
					<button type="button" class="ui-button btn-small">모든태그</button>
				</div>
			<?
			}
			?>
			<div class="tagList" id="tags"></div>
		</dd>
	</dl>
</fieldset>

<script>function log(o){console.log(o);}</script>
<script src="<?=$jQueryAddress?>"></script>
<script src="<?=$extPath?>/Jcrop/jquery.Jcrop.min.js"></script>
<script src="<?=$extPath?>/UploadInterface/FilesQueue.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/FileUpload.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/Thumnail.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/UploadInterface.class.js"></script>
<script src="<?=$extPath?>/TagManager/TagManager.class.js"></script>
<script>
jQuery(function($){
	var uploadInterface = new UploadInterface($('#fileUpload'), {
		form : document.writeForm
		,$queue : $('#queuesManager')
		,uploadAction : '<?=GOOSE_ROOT?>/files/upload/'
		,removeAction : '<?=GOOSE_ROOT?>/files/remove/'
		,fileDir : '<?=GOOSE_ROOT?>/data/original/'
		,auto : false
		,limit : 5
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

	// tag manager
	var tagManager = new TagManager($('#tag'), $('#tags'));
	$('[role-action=addTag]').on('click', function(){
		tagManager.add(tagManager.$input.val());
	});
	// all tags init
	tagManager.allTags($('#allTagsIndex'));

/*
	// importTag
	// $article['json']->tags
	// tagManager.import(tags);
*/

	// onsubmit event
	$(document.writeForm).on('submit', function(){
		var json = new Object();

		// thumnail image check
		if (uploadInterface.thumnailImageCheck())
		{
			return false;
		}

		// export tag data
		json.tag = tagManager.export();

		// json object to hidden string
		$(this).find('input[name=json]').val(JSON.stringify(json));
	});
});
</script>
