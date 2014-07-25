<?php
if(!defined("GOOSE")){exit();}

$json = null;
$json_srl = (isset($routePapameters['param0'])) ? (int)$routePapameters['param0'] : null;

if ($paramAction !== "create" and $json_srl)
{
	$json = $spawn->getItem(array(
		'table' => $tablesName['jsons'],
		'where' => 'srl='.$json_srl
	));
	$json['json'] = urlencode($json['json']);
}

$titleType = ($paramAction == 'create') ? '만들기' : '';
$titleType = ($paramAction == 'modify') ? '수정' : $titleType;
$titleType = ($paramAction == 'delete') ? '삭제' : $titleType;
?>

<link rel="stylesheet" type="text/css" href="<?=ROOT?>/libs/ext/JSONEditor/JSONEditor.css" media="screen" />

<section class="form JSONPost">
	<div class="hgroup">
		<h1>JSON <?=$titleType?></h1>
	</div>
	<form action="<?=ROOT?>/json/<?=$paramAction?>/" method="post" name="post" id="regsterForm">
		<input type="hidden" name="srl" value="<?=$json['srl']?>" />
		<?
		if ($paramAction == "delete")
		{
		?>
			<fieldset>
				<legend class="blind">JSON<?=$titleType?></legend>
				<p class="message">"<?=$json['name']?>" JSON 데이터를 삭제하시겠습니까? 삭제된 JSON 데이터는 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
			<input type="hidden" name="json" />
			<fieldset>
				<legend class="blind">JSON<?=$titleType?></legend>
				<dl class="table">
					<dt><label for="name">이름</label></dt>
					<dd><input type="text" name="name" id="name" size="22" maxlength="20" value="<?=$json['name']?>"/></dd>
				</dl>
				<dl>
					<dt><label>JSON DATA</label></dt>
					<dd>
						<div class="JSONEditor" id="JSONEditor"></div>
					</dd>
				</dl>
			</fieldset>
		<?
		}
		?>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>

<?
if ($paramAction != "delete")
{
?>
	<script src="<?=$jQueryAddress?>"></script>
	<script src="<?=ROOT?>/libs/ext/validation/jquery.validate.min.js"></script>
	<script src="<?=ROOT?>/libs/ext/validation/localization/messages_ko.js"></script>
	<script src="<?=ROOT?>/libs/ext/JSONEditor/jquery-sortable.js"></script>
	<script src="<?=ROOT?>/libs/ext/JSONEditor/JSONEditor.class.js"></script>
	<script>
	jQuery(function($){

		var $jsonEditor = $('#JSONEditor');
		var $form = $('#regsterForm');
		var jsonData = '<?=$json['json']?>';
		var jsonEditor = new JSONEditor($jsonEditor);
  
		// import json
		try {
			var json = JSON.parse(decodeURIComponent(jsonData));
		}
		catch(e) {
			var json = new Object();
		}
	
		if (Array.isArray(json))
		{
			jsonEditor.typeItem($jsonEditor.find('li[loc=root]'), 'Array');
		}
		jsonEditor.importJSON(json);
	
		// submit form
		$form.on('submit', function(){
			$(this).find('input[name=json]').val(jsonEditor.exportJSON(0));
		});
	
		$form.validate({
			rules : {
				name : {required : true, minlength : 3}
			}
		});

	});
	</script>
<?
}
?>