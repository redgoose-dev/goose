<?php
if(!defined("GOOSE")){exit();}

$pushData = null;
$extPath = GOOSE_ROOT.'/libs/ext';
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

<link rel="stylesheet" href="<?=GOOSE_ROOT?><?=$path_skin?>/css/UploadInterface.css" />
<link rel="stylesheet" href="<?=$extPath?>/Jcrop/jquery.Jcrop.min.css" />

<input type="hidden" name="addQueue" value="" />
<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
<input type="hidden" name="thumnail_image" value="" />
<input type="hidden" name="content" value="<?=$article['content']?>"/>

<section class="goose-form">
	<div class="hgroup">
		<h1><?=$nestName?>문서<?=$titleType?></h1>
	</div>

	<form name="writeForm" action="<?=GOOSE_ROOT?>/article/<?=$paramAction?>/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="group_srl" value="<?=$nest['group_srl']?>" />
		<input type="hidden" name="nest_srl" value="<?=$nest['srl']?>" />
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="page" value="<?=$_GET['page']?>" />
		<input type="hidden" name="json" />
		<input type="hidden" name="addQueue" value="" />
		<input type="hidden" name="thumnail_srl" value="<?=$article['thumnail_srl']?>" />
		<input type="hidden" name="thumnail_coords" value="<?=$article['thumnail_coords']?>" />
		<input type="hidden" name="thumnail_image" value="" />
		<input type="hidden" name="content" value="<?=$article['content']?>"/>
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
				<dt><label for="fileUpload">파일첨부</label></dt>
				<dd>
					<div class="box">
						<input type="file" name="fileUpload" id="fileUpload" multiple />
					</div>
				</dd>
			</dl>
			<div class="queuesManager" id="queuesManager"></div>
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
<script src="<?=$extPath?>/dragsort/jquery.dragsort-0.5.1.min.js"></script>
<script src="<?=GOOSE_ROOT?><?=$path_skin?>/js/FilesQueue.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/FileUpload.class.js"></script>
<script src="<?=$extPath?>/UploadInterface/Thumnail.class.js"></script>
<script src="<?=GOOSE_ROOT?><?=$path_skin?>/js/UploadInterface.class.js"></script>
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
		]
	});

	// push data
	var attachFiles = '<?=$pushData?>';
	attachFiles = (attachFiles) ? JSON.parse(attachFiles) : null;
	var contentData = $form.find('input[name=content]').val();
	try {
		contentData = (contentData) ? JSON.parse(decodeURIComponent(contentData)) : null;
	} catch(e) {
		contentData = null;
	}

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