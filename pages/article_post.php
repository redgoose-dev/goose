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

function extraKeyTypePrint($n=NULL, $keyName="", $keyValue="", $selectVar="")
{
	switch ($n)
	{
		// 한줄입력칸
		case 0:
			$value = ($selectVar) ? $selectVar : $keyValue;
			$str = "<span class='ipt-text ipt-block'><input type='text' name='$keyName' value='$value'/></span>";
			break;

		// 여러줄입력칸
		case 1:
			$value = ($selectVar) ? $selectVar : $keyValue;
			$str = "<span class='ipt-text ipt-ta ipt-block'><textarea name='$keyName'>$value</textarea></span>";
			break;

		// 단일선택
		case 2:
			$arr = explode(",", $keyValue);
			$str = "<span class='iptSelect'>";
			$str .= "<select name='$keyName' id='$keyName'>";
			$str .= "<option value=''>선택하세요.</option>";
			for ($i=0; $i<count($arr); $i++)
			{
				$str .= ($selectVar == $arr[$i]) ? "<option value='$arr[$i]' selected='selected'>$arr[$i]</option>" : "<option value='$arr[$i]'>$arr[$i]</option>";
			}
			$str .= "</select>";
			$str .= "</span>";
			break;
	}
	return $str;
}
?>

<section class="form">
	<div class="hgroup">
		<h1><?=$nestName?>문서<?=$titleType?></h1>
	</div>

	<form name="writeForm" action="<?=ROOT?>/article/<?=$paramAction?>/" method="post" enctype="multipart/form-data" onsubmit="return onCheck(this);">
		<input type="hidden" name="group_srl" value="<?=$nest[group_srl]?>" />
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="page" value="<?=$_GET[page]?>" />
		<input type="hidden" name="addQueue" />
		<input type="hidden" name="thumnail_srl" />
		<input type="hidden" name="thumnail_image" />
		<input type="hidden" name="thumnail_coords" value="<?=$article[thumnail_coords]?>" />
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
								if ($article[category_srl]==$v[srl] or $category_srl==$v[srl])
								{
									echo '<option value="'.$v[srl].'" selected>'.$v[name].'</option>';
								}
								else
								{
									echo '<option value="'.$v[srl].'">'.$v[name].'</option>';
								}
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
				'table' => $tablesName[extraKeys],
				'where' => 'nest_srl='.$nest[srl]
			));
			if ($extraCount > 0)
			{
		?>
				<!-- 확장변수 폼 -->
				<input type="hidden" name="useExtraVar" value="1" />
				<fieldset class='extraForm'>
					<h1>확장변수<?=$titleType?></h1>
					<ul>
						<?
						$items = $spawn->getItems(array(
							'table' => $tablesName[extraKeys],
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
							?>
							<li>
								<label for="<?=$v[keyName]?>"><?=$v[name]?></label>
								<?=extraKeyTypePrint($v[formType], 'ext_'.$v[keyName], $defaultValue, $extVar[value])?>
								<p><?=$v[info]?></p>
							</li>
							<?
						}
						?>
					</ul>
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
