<?php
if(!defined("GOOSE")){exit();}

$nestName = '['.$nest['name'].'] ';
$titleType = getActionType($paramAction);
?>

<section class="goose-form">
	<div class="hgroup">
		<h1><?=$nestName?>문서<?=$titleType?></h1>
	</div>

	<form name="writeForm" action="<?=GOOSE_ROOT?>/article/<?=$paramAction?>/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="group_srl" value="<?=$nest['group_srl']?>" />
		<input type="hidden" name="nest_srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="article_srl" value="<?=$article_srl?>" />
		<input type="hidden" name="page" value="<?=$_GET['page']?>" />
		<?
		if ($paramAction == 'modify')
		{
			$url = preg_replace('/\/modify\//', '/view/', $_SERVER['REQUEST_URI']);
			echo "<input type=\"hidden\" name=\"url\" value=\"$url\" />";
		}
		?>
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
							$items = $goose->spawn->getItems(array(
								'table' => categories,
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
			<dl>
				<dt><label for="content">내용</label></dt>
				<dd><textarea name="content" id="content" rows="15" class="block"><?=htmlspecialchars($article['content'])?></textarea></dd>
			</dl>
		</fieldset>

		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight"><?=$titleType?></button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>