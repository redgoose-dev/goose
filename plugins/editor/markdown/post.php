<?php
if(!defined("GOOSE")){exit();}

$path = ROOT.'/plugins/editor/'.$nest['editor'];

if ($paramAction == 'create')
{
	$attachFiles = $spawn->getItems(array(
		'table' => $tablesName['tempFiles'],
		'order' => 'srl',
		'sort' => 'asc'
	));
	$type = 'session';
}
else if ($paramAction == 'modify')
{
	// modify
	$attachFiles = $spawn->getItems(array(
		'table' => $tablesName['files'],
		'where' => 'article_srl='.$article_srl,
		'order' => 'srl',
		'sort' => 'asc'
	));
	$type = 'modify';
}

if (count($attachFiles))
{
	$pushData = array();
	foreach ($attachFiles as $k=>$v)
	{
		$item = array(
			'srl' => $v['srl']
			,'location' => $v['loc']
			,'filename' => $v['name']
			,'type' => $type
		);
		array_push($pushData, $item);
	}
	$pushData = json_encode($pushData);
}
?>

<link rel="stylesheet" href="<?=$path?>/css/post.css" />
<link rel="stylesheet" href="<?=$path?>/css/queuesManager.css" />

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
	<article class="queuesManager">
		<h1 class="blind">파일첨부 관리</h1>
		<div class="filesQueue" id="filesQueue">
			<figure class="thumnail"></figure>
			<ul></ul>
		</div>
		<nav id="queueController">
			<button type="button" rg-action="insertContents" class="ui-button btn-small btn-highlight">본문삽입</button>
			<button type="button" rg-action="useThumnail" class="ui-button btn-small">썸네일설정</button>
			<button type="button" rg-action="selectAll" class="ui-button btn-small">모두선택</button>
			<button type="button" rg-action="deleteSelect" class="ui-button btn-small">선택삭제</button>
			<button type="button" rg-action="deleteAll" class="ui-button btn-small">모두삭제</button>
		</nav>
	</article>
</fieldset>
<?
$util->console($nest);
?>
<script>function log(o){console.log(o);}</script>
<script src="<?=$jQueryAddress?>"></script>
<script src="<?=$path?>/lib/jquery.Jcrop.min.js"></script>
<script src="<?=$path?>/js/FilesQueue.class.js"></script>
<script src="<?=$path?>/js/FileUpload.class.js"></script>
<script src="<?=$path?>/js/Thumnail.class.js"></script>
<script src="<?=$path?>/js/UploadInterface.class.js"></script>
<script>
jQuery(function($){
	var
		uploadInterface = new UploadInterface($('#fileUpload'), {
			uploadAction : '<?=ROOT?>/files/upload/'
			,removeAction : '<?=ROOT?>/files/remove/'
			,fileDir : '<?=ROOT?>/data/original/'
			,auto : true
			,$queue : $('#filesQueue')
			,$controller : $('#queueController')
			,limit : 3
			,token : '<?=md5("uPloAD_toKEn" . time());?>'
			,content : $('#content')
			,thumnailType : '<?=$nest['thumnailType']?>'
			,thumnailSize : '<?=$nest['thumnailSize']?>'
		})
		,attachFiles = '<?=$pushData?>'
		,attachFilesData = (attachFiles) ? JSON.parse(attachFiles) : null
	;

	if (attachFilesData)
	{
		uploadInterface.pushQueue(attachFilesData);
	}

	// upload button click event
	$('#fileUploadButton').on('click', function(){
		uploadInterface.upload();
	});
});
</script>