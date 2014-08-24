<?php
if(!defined("GOOSE")){exit();}

$extPath = GOOSE_ROOT.'/libs/ext';
$tagPath = PWD.'/data/config/tags.user.txt';
require('attachFileDatas.php');

$nestName = '['.$nest['name'].'] ';
$titleType = getActionType($paramAction);

// all tags
if (file_exists($tagPath))
{
	$allTagsData = $goose->util->fop($tagPath, 'r');
}

// article tags
if (isset($article['json']))
{
	$articleTags = $article['json']['tag'];
	$articleTagsString = json_encode($articleTags);
}
?>

<link rel="stylesheet" href="<?=$extPath?>/UploadInterface/UploadInterface.css" />
<link rel="stylesheet" href="<?=$extPath?>/TagManager/TagManager.css" />
<link rel="stylesheet" href="<?=$extPath?>/Jcrop/jquery.Jcrop.min.css" />

<section class="goose-form">
	<div class="hgroup">
		<h1><?=$nestName?>문서<?=$titleType?></h1>
	</div>

	<form name="writeForm" action="<?=GOOSE_ROOT?>/article/<?=$paramAction?>/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="group_srl" value="<?=$nest['group_srl']?>" />
		<input type="hidden" name="nest_srl" value="<?=$nest['srl']?>" />
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="page" value="<?=$_GET['page']?>" />
		<input type="hidden" name="addQueue" value="" />
		<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
		<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
		<input type="hidden" name="thumnail_image" value="" />
		<input type="hidden" name="json" />
		<?
		if ($paramAction == 'modify')
		{
			$url = preg_replace('/\/modify\//', '/view/', $_SERVER['REQUEST_URI']);
			echo "<input type=\"hidden\" name=\"url\" value=\"$url\" />";
		}
		?>
		<fieldset>
			<legend class="blind">문서<?=$titleType?></legend>
			<?
			if ($nest['useCategory'] == 1)
			{
			?>
				<dl class="table">
					<dt><label for="category">분류</label></dt>
					<dd>
						<select name="category_srl" id="category">
							<option value="">분류선택</option>
							<?
							$items = $goose->spawn->getItems(array(
								'table' => categories,
								'where' => 'nest_srl='.$nest['srl'],
								'order' => 'turn',
								'sort' => 'asc'
							));
							foreach($items as $k=>$v)
							{
								$selected = ($article['category_srl']==$v['srl'] or $category_srl==$v['srl']) ? 'selected' : '';
								echo "<option value=\"$v[srl]\" $selected>$v[name]</option>";
							}
							?>
						</select>
					</dd>
				</dl>
			<?
			}
			?>

			<dl class="table">
				<dt><label for="title">제목</label></dt>
				<dd><input type="text" id="title" name="title" class="block" value="<?=$article['title']?>" /></dd>
			</dl>

			<dl>
				<dt><label for="content">내용</label></dt>
				<dd>
					<textarea name="content" id="content" rows="15" class="block"><?=htmlspecialchars($article[content])?></textarea>
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
					<button type="button" class="ui-button btn-small" role-action="removeAllTags">모두삭제</button>
					<?
					if (isset($allTagsData))
					{
						$allTagsData = json_decode($allTagsData);
						if (count($allTagsData))
						{
					?>
							<div class="tagIndexWrap" id="allTagsIndex">
								<div class="list">
									<ul>
										<?
										foreach ($allTagsData as $k=>$v)
										{
											echo "<li><span>$v->name</span><em>".count($v->srl)."</em></li>";
										}
										?>
									</ul>
								</div>
								<button type="button" class="ui-button btn-small">모든태그</button>
							</div>
					<?
						}
					}
					?>
					<div class="tagList" id="tags"></div>
				</dd>
			</dl>
		</fieldset>

		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>

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
	// add tag
	$('[role-action=addTag]').on('click', function(){
		tagManager.add(tagManager.$input.val());
	});
	// remove all tags
	$('[role-action=removeAllTags]').on('click', function(){
		if (confirm('모든 태그를 삭제하시겠습니까?'))
		{
			tagManager.remove($('#tags > p'));
		}
	});
	// importTags
	var articleTags = '<?=$articleTagsString?>';
	articleTags = (articleTags) ? JSON.parse(articleTags) : '';
	tagManager.import(articleTags);
	// all tags init
	tagManager.allTagsInit($('#allTagsIndex'));

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
