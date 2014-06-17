<?php
if(!defined("GOOSE")){exit();}

if ($routePapameters['param1'])
{
	$module_srl = (int)$routePapameters['param0'];
	$category_srl = (int)$routePapameters['param1'];
}
else if ($routePapameters['param0'])
{
	$module_srl = (int)$routePapameters['param0'];
}
else
{
	$util->back('값이 없습니다.');
	exit;
}

$module = $spawn->getItem(array(
	'field' => 'srl,group_srl,name',
	'table' => $tablesName[modules],
	'where' => 'srl='.$module_srl
));
$moduleName = ($module[name]) ? '['.$module[name].'] ' : null;

if ($paramAction !== 'create')
{

	if (!$category_srl)
	{
		$util->back('category값이 없습니다.');
		exit;
	}
	$category = $spawn->getItem(array(
		'table' => $tablesName[categories],
		'where' => 'srl='.$category_srl
	));
}

$titleType = getActionType($paramAction);
?>

<section class="form">
	<div class="hgroup">
		<h1><?=$moduleName?>분류<?=$titleType?></h1>
	</div>

	<form action="<?=ROOT?>/category/<?=$paramAction?>/" method="post" onsubmit="return onCheck(this); return false;">
		<input type="hidden" name="module_srl" value="<?=$module_srl?>"/>
		<input type="hidden" name="group_srl" value="<?=$module[group_srl]?>"/>
		<input type="hidden" name="category_srl" value="<?=$category_srl?>"/>
		<?
		if ($paramAction == "delete")
		{
		?>
			<script type="text/javascript">
			function onCheck(frm)
			{
				return true;
			}
			</script>
			<fieldset>
				<legend class="blind">분류<?=$titleType?></legend>
				<p class="message">"<?=$category[name]?>"분류를 삭제하시겠습니까? 삭제된 분류는 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
			<script type="text/javascript">
			function onCheck(frm)
			{
				if (!frm.name.value)
				{
					alert('이름 항목이 비었습니다.');
					frm.name.focus();
					return false;
				}
			}
			</script>
			<fieldset>
				<legend class="blind">분류<?=$titleType?></legend>
				<dl class="table">
					<dt><label for="name">이름</label></dt>
					<dd><input type="text" name="name" id="name" size="20" maxlength="20" value="<?=$category[name]?>"/></dd>
				</dl>
			</fieldset>
		<?
		}
		?>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">돌아가기</button></span>
		</nav>
	</form>
</section>