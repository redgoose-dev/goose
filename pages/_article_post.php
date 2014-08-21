<?php
if(!defined("GOOSE")){exit();}

$article = null;
$_GET['page'] = (isset($_GET['page'])) ? $_GET['page'] : null;

if ($paramAction == 'create')
{
	if (!$nest_srl)
	{
		$util->back('nest값이 없습니다.');
		exit;
	}
	$nest = $spawn->getItem(array(
		'table' => $tablesName['nests'],
		'where' => 'srl='.$nest_srl
	));
}
else if ($paramAction == 'modify')
{
	if (!$article_srl)
	{
		$util->back('article값이 없습니다.');
		exit;
	}
	$article = $spawn->getItem(array(
		'table' => $tablesName['articles'],
		'where' => 'srl='.$article_srl
	));
	$nest = $spawn->getItem(array(
		'table' => $tablesName['nests'],
		'where' => 'srl='.$article['nest_srl']
	));
	$nest_srl = $article['nest_srl'];
}

$nestName = '['.$nest['name'].'] ';
$titleType = getActionType($paramAction);
?>

<section class="form">
	<div class="hgroup">
		<h1><?=$nestName?>문서<?=$titleType?></h1>
	</div>

	<form name="writeForm" action="<?=GOOSE_ROOT?>/article/<?=$paramAction?>/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="group_srl" value="<?=$nest['group_srl']?>" />
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="page" value="<?=$_GET['page']?>" />
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
							$items = $spawn->getItems(array(
								'table' => $tablesName['categories'],
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
		</fieldset>

		<?
		// Import editor Plugin
		$editorDir = PWD.'/plugins/editor/';
		if (file_exists($editorDir.$nest['editor'].'/post.php'))
		{
			require_once($editorDir.$nest['editor'].'/post.php');
		}
		else
		{
			if (file_exists($editorDir.'basic/post.php'))
			{
				require_once($editorDir.'basic/post.php');
			}
		}
		?>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>