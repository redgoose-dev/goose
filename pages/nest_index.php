<?php
if(!defined("GOOSE")){exit();}

$group_srl = (isset($routePapameters['param0'])) ? (int)$routePapameters['param0'] : null;
$itemParameter = ($group_srl) ? 'group_srl='.$group_srl : '';

$nestsCount = $spawn->getCount(array('table'=>$tablesName['nests'], 'where'=>$itemParameter));
$nestsIndex = $spawn->getItems(array(
	'table' => $tablesName['nests'],
	'where' => $itemParameter,
	'order' => 'srl',
	'sort' => 'desc'
));
$nestGroupsCount = $spawn->getCount(array('table'=>$tablesName['nestGroups']));
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=ROOT?>/nest/index/">Nests</a></h1>
	</div>
	<?
	if ($nestGroupsCount)
	{
	?>
		<!-- groups list -->
		<nav class="categories">
			<ul>
				<?
				$nestGroupsIndex = $spawn->getItems(array(
					'table' => $tablesName[nestGroups],
					'order' => 'srl',
					'sort' => 'desc'
				));
				foreach ($nestGroupsIndex as $k=>$v)
				{
					$nestCount = $spawn->getCount(array(
						'table' => $tablesName[nests],
						'where' => 'group_srl='.(int)$v[srl]
					));
					$active = ($group_srl == $v[srl]) ? " class='active'" : "";
					$url = ROOT.'/nest/index/'.$v[srl].'/';
					echo "
						<li $active>
							<a href=\"$url\">$v[name]($nestCount)</a>
						</li>
					";
				}
				?>
			</ul>
		</nav>
		<!-- // groups list -->
	<?
	}
	?>

	<!-- nests list -->
	<ul class="index">
		<?
		if ($nestsCount > 0)
		{
			foreach ($nestsIndex as $k=>$v)
			{
				$url = ROOT.'/article/index/'.$v[srl].'/';
				$articleCount = $spawn->getCount(array(
					'table' => $tablesName[articles],
					'where' => 'nest_srl='.(int)$v[srl]
				));
				$categoryCount = $spawn->getCount(array(
					'table' => $tablesName[categories],
					'where' => 'nest_srl='.(int)$v[srl]
				));
				$categoryCount = ($categoryCount && $v[useCategory]==1) ? '<span>분류:'.$categoryCount.'</span>' : '';
				$groupName = $spawn->getItem(array(
					'field' => 'name',
					'table' => $tablesName[nestGroups],
					'where' => 'srl='.(int)$v[group_srl]
				));
				$groupName = ($groupName[name]) ? "<em>[".$groupName[name]."]</em>" : "";
				$categoryBtn = ($v[useCategory] == 1) ? '<a href="'.ROOT.'/category/index/'.$v[srl].'/">분류설정</a>' : '';
				$extraVarBtn = ($v[useExtraVar] == 1) ? '<a href="'.ROOT.'/extrakey/index/'.$v[srl].'/">확장변수설정</a>' : '';
		?>
				<li>
					<div class="body">
						<a href="<?=$url?>">
							<strong><?=$groupName?> <?=$v[name]?>(<?=$articleCount?>)</strong>
						</a>
						<div class="inf">
							<span>아이디:<?=$v[id]?></span>
							<span>날짜:<?=$util->convertDate($v[regdate])?></span>
							<?=$categoryCount?>
							<span>썸네일사이즈:<?=$v[thumnailSize]?></span>
						</div>
						<nav>
							<a href="<?=ROOT?>/nest/modify/<?=$v[srl]?>/">수정</a>
							<a href="<?=ROOT?>/nest/delete/<?=$v[srl]?>/">삭제</a>
							<?=$categoryBtn?>
							<?=$extraVarBtn?>
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
	<!-- // nests list -->
	<!-- bottom buttons -->
	<nav class="btngroup">
		<span><a href="<?=ROOT?>/nest/index/" class="ui-button">목록</a></span>
		<span><a href="<?=ROOT?>/group/index/" class="ui-button">둥지그룹</a></span>
		<span><a href="<?=ROOT?>/nest/create/" class="ui-button btn-highlight">둥지만들기</a></span>
	</nav>
	<!-- // bottom buttons -->
</section>