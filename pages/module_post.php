<?php
if(!defined("GOOSE")){exit();}

$module_srl = ($routePapameters['param0']) ? (int)$routePapameters['param0'] : null;
if ($paramAction !== "create" and $module_srl)
{
	$module = $spawn->getItem(array(
		'table' => $tablesName[modules],
		'where' => 'srl='.$module_srl
	));
	$thumnailSize = explode("*", $module[thumnailSize]);
}

if ($thumnailSize[1])
{
	$thumnailSize = array('width' => $thumnailSize[0], 'height' => $thumnailSize[1]);
}
else
{
	$thumnailSize = array('width'=>100, 'height'=>100);
}

$listCount = ($module[listCount]) ? $module[listCount] : 12;
$titleType = ($paramAction == 'create') ? '만들기' : '';
$titleType = ($paramAction == 'modify') ? '수정' : $titleType;
$titleType = ($paramAction == 'delete') ? '삭제' : $titleType;
?>

<section class="form">
	<div class="hgroup">
		<h1>모듈<?=$titleType?></h1>
	</div>
	<form action="<?=ROOT?>/module/<?=$paramAction?>/" method="post" onsubmit="return onCheck(this);">
		<input type="hidden" name="group_srl" value="<?=$module[group_srl]?>" />
		<input type="hidden" name="module_srl" value="<?=$module_srl?>" />
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
				<legend class="blind">모듈<?=$titleType?></legend>
				<p class="message">"<?=$module[name]?>"모듈을 삭제하시겠습니까? 삭제된 모듈은 복구할 수 없습니다.</p>
			</fieldset>
		<?
		}
		else
		{
		?>
			<script type="text/javascript">
			function onCheck(frm)
			{
				if (!frm.id.value)
				{
					alert('모듈아이디 항목이 비었습니다.');
					frm.id.focus();
					return false;
				}
				
				if (!frm.id.value.match(/^[a-zA-Z0-9]+$/))
				{
					alert('모듈아이디는 영문과 숫자로 작성해주세요.');
					frm.id.focus();
					return false;
				}

				if (!frm.name.value)
				{
					alert('모듈이름 항목이 비었습니다.');
					frm.name.focus();
					return false;
				}
				
				if (!frm.listCount.value.match(/^[0-9]+$/))
				{
					alert('번호로 써주세요.');
					frm.listCount.focus();
					return false;
				}
				
				return true;
			}
			</script>
			<fieldset>
				<legend class="blind">모듈<?=$titleType?></legend>
				<dl class="table">
					<dt><label for="group_srl">모듈그룹</label></dt>
					<dd>
						<select name="group_srl" id="group_srl">
							<option value="0">선택하세요.</option>
							<?
							$group = $spawn->getItems(array(
								'table' => $tablesName[moduleGroups],
								'order' => 'srl',
								'sort' => 'asc'
							));
							foreach ($group as $k=>$v)
							{
								$selected = ($v[srl] == $module[group_srl]) ? ' selected="selected"' : '';
								echo "<option value=\"$v[srl]\"$selected>$v[name]</option>";
							}
							?>
						</select>
					</dd>
				</dl>
				<dl class="table">
					<?
					if ($module[id])
					{
						$attr = ' value="'.$module[id].'" readonly';
					}
					?>
					<dt><label for="id">아이디</label></dt>
					<dd>
						<input type="text" name="id" id="id" maxlength="20" size="22" placeholder="영문과 숫자 입력가능"<?=$attr?>/>
						<p>이 항목은 한번정하면 변경할 수 없습니다.</p>
					</dd>
				</dl>
				<dl class="table">
					<dt><label for="name">모듈이름</label></dt>
					<dd><input type="text" name="name" id="name" maxlength="100" size="22" value="<?=$module[name]?>"/></dd>
				</dl>
				<dl class="table">
					<dt><label for="thumWidth">썸네일사이즈</label></dt>
					<dd>
						<input type="text" name="thumWidth" id="thumWidth" maxlength="4" size="5" value="<?=$thumnailSize[width]?>"/>
						*
						<input type="text" name="thumHeight" id="thumHeight" maxlength="4" size="5" value="<?=$thumnailSize[height]?>"/>
					</dd>
				</dl>
				<dl class="table">
					<dt><label>썸네일 축소방식</label></dt>
					<dd>
						<?
						if ($paramAction == "modify")
						{
							$thumType1 = ($module[thumnailType] == "crop") ? ' checked = "checked"' : '';
							$thumType2 = ($module[thumnailType] == "resize") ? ' checked = "checked"' : '';
							$thumType3 = ($module[thumnailType] == "resizeWidth") ? ' checked = "checked"' : '';
							$thumType4 = ($module[thumnailType] == "resizeHeight") ? ' checked = "checked"' : '';
						}
						else
						{
							$thumType1 = ' checked = "checked"';
						}
						?>
						
							<label><input type="radio" name="thumType" id="thumType1" value="crop"<?=$thumType1?>/> 자르기</label>
							<label><input type="radio" name="thumType" id="thumType2" value="resize"<?=$thumType2?>/> 리사이즈</label>
							<label><input type="radio" name="thumType" id="thumType3" value="resizeWidth"<?=$thumType3?>/> 리사이즈(가로기준)</label>
							<label><input type="radio" name="thumType" id="thumType4" value="resizeHeight"<?=$thumType4?>/> 리사이즈(세로기준)</label>
						
					</dd>
				</dl>
				<dl class="table">
					<dt><label for="listCount">목록수</label></dt>
					<dd>
						<input type="tel" name="listCount" id="listCount" maxlength="3" size="4" value="<?=$listCount?>"/>
						<p>한페이지에 출력되는 글 갯수입니다.</p>
					</dd>
				</dl>
				<dl class="table">
					<?
					$useCategoryYes = ($module[useCategory] == 1) ? ' checked = "checked"' : '';
					$useCategoryNo = ($module[useCategory] != 1) ? ' checked = "checked"' : '';
					?>
					<dt><label for="useCategory">분류사용</label></dt>
					<dd>
						<label><input type="radio" name="useCategory" id="useCategory" value="1" <?=$useCategoryYes?>/> 사용</label>
						<label><input type="radio" name="useCategory" value="0" <?=$useCategoryNo?>/> 사용안함</label>
					</dd>
				</dl>
				<dl class="table">
					<?
					$useExtraVarYes = ($module[useExtraVar] == 1) ? ' checked = "checked"' : '';
					$useExtraVarNo = ($module[useExtraVar] != 1) ? ' checked = "checked"' : '';
					?>
					<dt><label for="useExtraVar">확장변수사용</label></dt>
					<dd>
						<label><input type="radio" name="useExtraVar" id="useExtraVar" value="1" <?=$useExtraVarYes?>/> 사용</label>
						<label><input type="radio" name="useExtraVar" value="0" <?=$useExtraVarNo?>/> 사용안함</label>
					</dd>
				</dl>
				<dl class="table">
					<dt><label for="editor">에디터 선택</label></dt>
					<dd>
						<select name="editor" id="editor">
							<?
							$tree = $util->readDir(PWD.'/plugins/editor/');
							echo (!count($tree)) ? "<option>에디터 없음</option>" : "";
							foreach($tree as $k=>$v)
							{
								$selected = ($v == $module['editor']) ? ' selected' : '';
								echo "<option value=\"$v\"$selected>$v</option>";
							}
							?>
						</select>
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