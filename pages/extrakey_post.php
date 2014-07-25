<?php
if(!defined("GOOSE")){exit();}

if ($routePapameters['param1'])
{
	$nest_srl = (int)$routePapameters['param0'];
	$extra_srl = (int)$routePapameters['param1'];
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

// get nest
$nest = $spawn->getItem(array(
	'table' => $tablesName[nests],
	'where' => 'srl='.$nest_srl
));
$nestName = ($nest[name]) ? '['.$nest[name].']' : '';

if ($paramAction != 'create')
{
	if (!$extra_srl)
	{
		$util->back('extrakey값이 없습니다.');
		$util->out();
	}
	$extrakey = $spawn->getItem(array(
		'table' => $tablesName[extraKey],
		'where' => 'srl='.$extra_srl
	));
}

$titleType = getActionType($paramAction);
?>

<section class="form">
	<div class="hgroup">
		<h1><?=$nestName?> 확장변수 <?=$titleType?></h1>
	</div>
	
	<form action="<?=GOOSE_ROOT?>/extrakey/<?=$paramAction?>/" method="post" id="regsterForm">
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="group_srl" value="<?=$nest[group_srl]?>" />
		<input type="hidden" name="extra_srl" value="<?=$extra_srl?>" />
		<?
		if ($paramAction == "delete")
		{
		?>
			<fieldset>
				<legend class="blind">확장변수 <?=$titleType?></legend>
				<p class="message">이 확장변수를 삭제하시겠습니까? 삭제된 확장변수는 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
			<fieldset>
				<legend class="blind">확장변수 <?=$titleType?></legend>
				<dl class="table">
					<dt><label for="keyName">확장변수이름</label></dt>
					<dd><input type="text" name="keyName" id="keyName" maxlength="20" size="20" value="<?=$extrakey[keyName]?>" placeholder="영문이나 숫자"/></dd>
				</dl>
				<dl class="table">
					<dt><label for="name">입력항목이름</label></dt>
					<dd><input type="text" name="name" id="name" size="20" value="<?=$extrakey[name]?>"/></dd>
				</dl>
				<dl class="table">
					<dt><label for="formType">형식</label></dt>
					<dd>
						<select name="formType" id="formType">
							<?
							for ($i=0; $i<count($extraKeyTypeArray); $i++)
							{
								$selected = ($extrakey[formType] == $i) ? 'selected' : '';
								echo "<option value='$i' $selected>$extraKeyTypeArray[$i]</option>";
							}
							?>
						</select>
					</dd>
				</dl>
				<dl class="table">
					<dt><label for="required">필수항목</label></dt>
					<dd>
						<?
						$check1 = ($extrakey[required] != '1') ? 'checked' : '';
						$check2 = ($extrakey[required] == '1') ? 'checked' : '';
						?>
						<label>기본값 <input type="radio" name="required" value="0" <?=$check1?>/></label>
						&nbsp;&nbsp;
						<label>필수 <input type="radio" name="required" value="1" <?=$check2?>/></label>
					</dd>
				</dl>
				<dl class="table">
					<dt><label for="defaultValue">기본값</label></dt>
					<dd>
						<input type="text" name="defaultValue" id="defaultValue" value="<?=$extrakey[defaultValue]?>" class="block"/>
						<p>여러항목을 사용하는 값을 구분하는 키워드는 [,]입니다.</p>
					</dd>
				</dl>
				<dl class="table">
					<dt><label for="info">설명</label></dt>
					<dd><input type="text" name="info" id="info" value="<?=$extrakey[info]?>" class="block"/></dd>
				</dl>
			</fieldset>
		<?
		}
		?>
		<div class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">돌아가기</button></span>
		</div>
	</form>
</section>

<?
if ($paramAction != "delete")
{
?>
	<script src="<?=$jQueryAddress?>"></script>
	<script src="<?=GOOSE_ROOT?>/libs/ext/validation/jquery.validate.min.js"></script>
	<script src="<?=GOOSE_ROOT?>/libs/ext/validation/localization/messages_ko.js"></script>
	<script>
	jQuery(function($){
		$.validator.addMethod("alphanumeric", function(value, element) {
			return this.optional(element) || /^[a-zA-Z0-9]+$/.test(value);
		});
		$('#regsterForm').validate({
			rules : {
				keyName : {required : true, minlength : 4, alphanumeric : true}
				,name : {required : true, minlength : 2}
			}
			,messages : {
				keyName : {alphanumeric: '알파벳과 숫자만 사용가능합니다.'}
			}
		});
	});
	</script>
<?
}
?>