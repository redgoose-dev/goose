<?php
if(!defined("GOOSE")){exit();}

if ($routePapameters['param1'])
{
	$nest_srl = (int)$routePapameters['param0'];
	$category_srl = (int)$routePapameters['param1'];
}
else if ($routePapameters['param0'])
{
	$nest_srl = (int)$routePapameters['param0'];
}
else
{
	$util->back('값이 없습니다.');
	$util->out();
}

$nest = $spawn->getItem(array(
	'field' => 'srl,group_srl,name',
	'table' => $tablesName[nests],
	'where' => 'srl='.$nest_srl
));
$nestName = ($nest[name]) ? '['.$nest[name].'] ' : null;

if ($paramAction !== 'create')
{
	if (!$category_srl)
	{
		$util->back('category_srl값이 없습니다.');
		$util->out();
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
		<h1><?=$nestName?>분류<?=$titleType?></h1>
	</div>

	<form action="<?=ROOT?>/category/<?=$paramAction?>/" method="post" id="regsterForm">
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>"/>
		<input type="hidden" name="group_srl" value="<?=$nest[group_srl]?>"/>
		<input type="hidden" name="category_srl" value="<?=$category_srl?>"/>
		<?
		if ($paramAction == "delete")
		{
		?>
			<fieldset>
				<legend class="blind">분류<?=$titleType?></legend>
				<p class="message">"<?=$category[name]?>"분류를 삭제하시겠습니까? 삭제된 분류는 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
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

<?
if ($paramAction != "delete")
{
?>
	<script src="<?=$jQueryAddress?>"></script>
	<script src="<?=ROOT?>/pages/src/pkg/validation/jquery.validate.min.js"></script>
	<script src="<?=ROOT?>/pages/src/pkg/validation/localization/messages_ko.js"></script>
	<script>
	jQuery('#regsterForm').validate({
		rules : {
			name : {required : true, minlength : 3}
		}
	});
	</script>
<?
}
?>