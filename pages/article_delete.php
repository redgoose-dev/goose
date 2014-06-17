<?php
if(!defined("GOOSE")){exit();}

$article_srl = (int)$routePapameters['param0'];
if (!$article_srl)
{
	$util->back('article값이 없습니다.');
	exit;
}

$article = $spawn->getItem(array(
	'field' => 'srl,module_srl,category_srl,title',
	'table' => $tablesName[articles],
	'where' => 'srl='.$article_srl
));

$module = $spawn->getItem(array(
	'table' => $tablesName[modules],
	'where' => 'srl='.$article[module_srl]
));
$moduleName = '['.$module[name].'] ';

$titleType = getActionType($paramAction);
?>

<section class="form">
	<div class="hgroup">
		<h1><?=$moduleName?>문서<?=$titleType?></h1>
	</div>
	<form name="writeForm" action="<?=ROOT?>/article/delete/" method="post">
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="module_srl" value="<?=$article[module_srl]?>" />
		<input type="hidden" name="category_srl" value="<?=$article[category_srl]?>" />
		<input type="hidden" name="page" value="<?=$_GET[page]?>" />
		<fieldset>
			<legend class="blind">문서<?=$titleType?></legend>
			<p class="message">"<?=$article[title]?>" 문서을 삭제하시겠습니까? 삭제된 문서는 복구할 수 없습니다.</p>
		</fieldset>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>
