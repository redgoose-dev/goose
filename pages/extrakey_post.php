<?
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
	exit;
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
		exit;
	}
	$extrakey = $spawn->getItem(array(
		'table' => $tablesName[extraKeys],
		'where' => 'srl='.$extra_srl
	));
}

$titleType = getActionType($paramAction);
?>

<section class="form">
	<div class="hgroup">
		<h1><?=$nestName?> 확장변수 <?=$titleType?></h1>
	</div>
	
	<form action="<?=ROOT?>/extrakey/<?=$paramAction?>/" method="post" onsubmit="return onCheck(this); return false;">
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="group_srl" value="<?=$nest[group_srl]?>" />
		<input type="hidden" name="extra_srl" value="<?=$extra_srl?>" />
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
				<legend class="blind">확장변수 <?=$titleType?></legend>
				<p class="message">이 확장변수를 삭제하시겠습니까? 삭제된 확장변수는 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
			<script type="text/javascript">
			function onCheck(frm)
			{
				if (!frm.keyName.value)
				{
					alert('확장변수이름 항목이 비었습니다.');
					frm.keyName.focus();
					return false;
				}
			
				if (!frm.keyName.value.match(/^[a-zA-Z0-9]+$/))
				{
					alert('확장변수이름은 영문으로 작성해주세요.');
					frm.keyName.focus();
					return false;
				}
				
				if (frm.keyName.value.length < 4)
				{
					alert('확장변수이름의 글자수가 4자 이상이어야합니다.');
					frm.keyName.focus();
					return false;
				}
			
				if (!frm.name.value)
				{
					alert('입력항목이름 항목이 비었습니다.');
					frm.name.focus();
					return false;
				}
			}
			</script>

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
								if ($extrakey[formType] == $i)
								{
									echo "<option value='$i' selected='selected'>$extraKeyTypeArray[$i]</option>";
								}
								else
								{
									echo "<option value='$i'>$extraKeyTypeArray[$i]</option>";
								}
							}
							?>
						</select>
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