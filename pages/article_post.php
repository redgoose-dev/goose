<?php
if(!defined("GOOSE")){exit();}

if ($paramAction == 'create')
{
	if (!$nest_srl)
	{
		$util->back('nest값이 없습니다.');
		exit;
	}
	$nest = $spawn->getItem(array(
		'table' => $tablesName[nests],
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
		'table' => $tablesName[articles],
		'where' => 'srl='.$article_srl
	));
	$nest = $spawn->getItem(array(
		'table' => $tablesName[nests],
		'where' => 'srl='.$article[nest_srl]
	));
	$nest_srl = $article[nest_srl];
}

$nestName = '['.$nest[name].'] ';
$titleType = getActionType($paramAction);

/**
 확장변수 폼 만들어주는 함수
 */
function extraKeyTypePrint($n=NULL, $keyName="", $keyValue="", $selectVar="", $required=null)
{
	$requiredAttr = ($required == 1) ? 'required' : '';

	switch ($n)
	{
		// 한줄입력칸 (input[type=text])
		case 0:
			$value = ($selectVar) ? $selectVar : $keyValue;
			$str = "<input type=\"text\" name=\"$keyName\" id=\"$keyName\" value=\"$value\" class=\"block\" $requiredAttr />";
			break;

		// 여러줄입력칸 (textarea)
		case 1:
			$value = ($selectVar) ? $selectVar : $keyValue;
			$str = "<textarea name=\"$keyName\" id=\"$keyName\" class=\"block\" $requiredAttr>$value</textarea>";
			break;

		// 단일선택 (select)
		case 2:
			$arr = explode(",", $keyValue);
			$str .= "<select name=\"$keyName\" id=\"$keyName\" $requiredAttr>";
			$str .= "<option value=\"\">선택하세요.</option>";
			for ($i=0; $i<count($arr); $i++)
			{
				$selected = ($selectVar == $arr[$i]) ? 'selected' : '';
				$str .= "<option value=\"$arr[$i]\" $selected>$arr[$i]</option>";
			}
			$str .= "</select>";
			break;
	}
	return $str;
}
?>

<script src="<?=$jQueryAddress?>"></script>

<section class="form">
	<div class="hgroup">
		<h1><?=$nestName?>문서<?=$titleType?></h1>
	</div>

	<form name="writeForm" id="regsterForm" action="<?=ROOT?>/article/<?=$paramAction?>/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="group_srl" value="<?=$nest[group_srl]?>" />
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="page" value="<?=$_GET[page]?>" />
		<fieldset>
			<legend class="blind">문서<?=$titleType?></legend>
			<?
			if ($nest[useCategory] == 1)
			{
			?>
				<dl class="table">
					<dt><label for="category">분류</label></dt>
					<dd>
						<select name="category_srl" id="category">
							<option value="">분류선택</option>
							<?
							$items = $spawn->getItems(array(
								table => $tablesName[categories],
								where => 'nest_srl='.$nest[srl],
								order => 'turn',
								sort => 'asc'
							));
							foreach($items as $k=>$v)
							{
								$selected = ($article[category_srl]==$v[srl] or $category_srl==$v[srl]) ? 'selected' : '';
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
				<dd><input type="text" id="title" name="title" class="block" value="<?=$article[title]?>" /></dd>
			</dl>
		</fieldset>

		<?
		// Import editor Plugin
		$editorDir = PWD.'/plugins/editor/';
		if (file_exists($editorDir.$nest['editor'].'/index.php'))
		{
			require_once($editorDir.$nest['editor'].'/index.php');
		}
		else
		{
			if (file_exists($editorDir.'basic/index.php'))
			{
				require_once($editorDir.'basic/index.php');
			}
		}

		// Extra var
		if ($nest[useExtraVar] == 1)
		{
			$extraCount = $spawn->getCount(array(
				'table' => $tablesName[extraKey],
				'where' => 'nest_srl='.$nest[srl]
			));
			if ($extraCount > 0)
			{
		?>
				<!-- 확장변수 폼 -->
				<input type="hidden" name="useExtraVar" value="1" />
				<fieldset style="margin-top:30px">
					<legend>추가 입력항목</legend>
					<?
					$items = $spawn->getItems(array(
						'table' => $tablesName[extraKey],
						'where' => 'nest_srl='.$nest[srl],
						'order' => 'turn',
						'sort' => 'asc'
					));
					foreach($items as $k=>$v)
					{
						$extVar = ($article[srl]) ? $spawn->getItem(array(
							'table' => $tablesName[extraVars],
							'where' => 'article_srl='.$article[srl].' and key_srl='.(int)$v[srl]
						)) : null;
						$defaultValue = ($paramAction=='create' or $v[formType]==2) ? $v[defaultValue] : '';
						$requiredClass = ($v[required]) ? 'class="required"' : '';
						?>
						<dl class="table<?=($k==0)?' first':''?>">
							<dt>
								<label for="ext_<?=$v[keyName]?>" <?=$requiredClass?>><?=$v[name]?></label>
							</dt>
							<dd>
								<?=extraKeyTypePrint($v[formType], 'ext_'.$v[keyName], $defaultValue, $extVar[value], $v[required])?>
								<p><?=$v[info]?></p>
							</dd>
						</dl>
						<?
					}
					?>
				</fieldset>
				<!-- // 확장변수 폼 -->
		<?
			}
		}
		?>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</article>

<script src="<?=ROOT?>/pages/src/pkg/validation/jquery.validate.min.js"></script>
<script src="<?=ROOT?>/pages/src/pkg/validation/localization/messages_ko.js"></script>
<script>
jQuery('#regsterForm').validate({
	rules : {
		title : {required : true, minlength : 5}
		,content : {required : true}
	}
});
</script>