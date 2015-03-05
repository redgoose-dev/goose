<?php
if(!defined("GOOSE")){exit();}

$pushData = null;
$extPath = GOOSE_ROOT.'/libs/ext';
require('attachFileDatas.php');

$nestName = '['.$nest['name'].'] ';
$titleType = getActionType($paramAction);
?>

<link rel="stylesheet" href="<?=GOOSE_ROOT?><?=$path_skin?>/css/UploadInterface.css" />
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
		<input type="hidden" name="addQueue" />
		<input type="hidden" name="thumnail_image" />
		<input type="hidden" name="content" value="<?=$article['content']?>"/>
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
<script src="<?=GOOSE_ROOT?><?=$path_skin?>/js/FilesQueue.class.min.js"></script>
<script src="<?=$extPath?>/UploadInterface/FileUpload.class.min.js"></script>
<script src="<?=$extPath?>/UploadInterface/Thumnail.class.min.js"></script>
<script src="<?=GOOSE_ROOT?><?=$path_skin?>/js/UploadInterface.class.min.js"></script>
<script>
window.userData = {
	root : '<?=GOOSE_ROOT?>'
	,url : '<?=GOOSE_URL?>'
	,thumnail : {
		type : '<?=$nest['json']['thumnail']['type']?>'
		,size : '<?=$nest['json']['thumnail']['size'][0].'*'.$nest['json']['thumnail']['size'][1]?>'
		,srl : '<?=$article['json']['thumnail']['srl']?>'
		,coords : '<?=$article['json']['thumnail']['coords']?>'
		,url : '<?=$article['json']['thumnail']['url']?>'
	}
	,pushData : '<?=$pushData?>'
};
</script>
<script src="<?=GOOSE_ROOT?><?=$path_skin?>/js/post.min.js"></script>
