<?php
if(!defined("GOOSE")){exit();}

// index
if ($paramAction == 'index')
{
	$itemCount = $spawn->getCount(array('table'=>$tablesName[nestGroups]));
?>
	<section>
		<div class="hgroup">
			<h1>둥지그룹 목록</h1>
		</div>
		<ul class="index">
			<?
			if ($itemCount > 0)
			{
				$itemIndex = $spawn->getItems(array(
					'table' => $tablesName[nestGroups],
					'order' => 'srl',
					'sort' => 'asc'
				));
				foreach ($itemIndex as $k=>$v)
				{
					$nestCount = $spawn->getCount(array(
						'table' => $tablesName[nests],
						'where' => 'group_srl='.(int)$v[srl]
					));
				?>
					<li>
						<div class="body">
							<strong><?=$v[srl]?> - <?=$v[name]?>(<?=$nestCount?>)</strong>
							<nav>
								<a href="<?=ROOT?>/group/modify/<?=$v[srl]?>/">수정</a>
								<a href="<?=ROOT?>/group/delete/<?=$v[srl]?>/">삭제</a>
							</nav>
						</div>
					</li>
				<?
				}
			}
			else
			{
				echo '<li class="empty">데이터가 없습니다.</li>';
			}
			?>
		</ul>
		<nav class="btngroup">
			<span><a href="<?=ROOT?>/group/create/" class="ui-button btn-highlight">그룹추가</a></span>
			<span><a href="<?=ROOT?>/nest/index/" class="ui-button">둥지목록</a></span>
		</nav>
	</section>
<?
}

// create, modify, delete
else
{
	$group_srl = ($routePapameters['param0']) ? (int)$routePapameters['param0'] : null;
	$titleType = ($paramAction == 'create') ? '만들기' : '';
	$titleType = ($paramAction == 'modify') ? '수정' : $titleType;
	$titleType = ($paramAction == 'delete') ? '삭제' : $titleType;
?>
	<section class="form">
		<div class="hgroup">
			<h1>둥지그룹 <?=$titleType?></h1>
		</div>
		<form action="<?=ROOT?>/group/<?=$paramAction?>/" method="post" onsubmit="return onCheck(this); return false;">
			<input type="hidden" name="group_srl" value="<?=$group_srl?>"/>
			<?
			if ($group_srl)
			{
				$group = $spawn->getItem(array(
					'field' => 'name',
					'table' => $tablesName[nestGroups],
					'where' => 'srl='.$group_srl
				));
			}

			if ($paramAction == 'delete')
			{
			?>
				<script type="text/javascript">
				function onCheck(frm)
				{
					return true;
				}
				</script>
				<fieldset class="delete">
					<legend class="blind">테이블 그룹<?=$postTitleArray[$type]?></legend>
					<p class="message">"<?=$group[name]?>"그룹를 삭제하시겠습니까? 삭제된 그룹은 복구할 수 없습니다.</p>
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
					<legend class="blind">그룹<?=$titleType?></legend>
					<dl class="table">
						<dt><label for="name">이름</label></dt>
						<dd><input type="text" name="name" id="name" size="20" maxlength="20" value="<?=$group[name]?>"/></dd>
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
}
?>