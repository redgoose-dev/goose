<?php
if(!defined("GOOSE")){exit();}

$group_srl = (int)$routePapameters['param0'];
$itemParameter = ($group_srl) ? 'group_srl='.$group_srl : '';

$modulesCount = $spawn->getCount(array('table'=>$tablesName[modules], 'where'=>$itemParameter));
$modulesIndex = $spawn->getItems(array(
	'table' => $tablesName[modules],
	'where' => $itemParameter,
	'order' => 'srl',
	'sort' => 'desc'
));
$moduleGroupsCount = $spawn->getCount(array('table'=>$tablesName[moduleGroups]));
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=ROOT?>/module/index/">Modules</a></h1>
	</div>
	<?
	if ($moduleGroupsCount)
	{
	?>
		<!-- groups list -->
		<nav class="categories">
			<ul>
				<?
				$moduleGroupsIndex = $spawn->getItems(array(
					'table' => $tablesName[moduleGroups],
					'order' => 'srl',
					'sort' => 'desc'
				));
				foreach ($moduleGroupsIndex as $k=>$v)
				{
					$moduleCount = $spawn->getCount(array(
						'table' => $tablesName[modules],
						'where' => 'group_srl='.(int)$v[srl]
					));
					$active = ($group_srl == $v[srl]) ? " class='active'" : "";
					$url = ROOT.'/module/index/'.$v[srl].'/';
					echo "
						<li $active>
							<a href=\"$url\">$v[name]($moduleCount)</a>
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

	<!-- modules list -->
	<ul class="index">
		<?
		if ($modulesCount > 0)
		{
			foreach ($modulesIndex as $k=>$v)
			{
				$url = ROOT.'/article/index/'.$v[srl].'/';
				$articleCount = $spawn->getCount(array(
					'table' => $tablesName[articles],
					'where' => 'module_srl='.(int)$v[srl]
				));
				$categoryCount = $spawn->getCount(array(
					'table' => $tablesName[categories],
					'where' => 'module_srl='.(int)$v[srl]
				));
				$categoryCount = ($categoryCount && $v[useCategory]==1) ? '<span>분류:'.$categoryCount.'</span>' : '';
				$groupName = $spawn->getItem(array(
					'field' => 'name',
					'table' => $tablesName[moduleGroups],
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
							<a href="<?=ROOT?>/module/modify/<?=$v[srl]?>/">수정</a>
							<a href="<?=ROOT?>/module/delete/<?=$v[srl]?>/">삭제</a>
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
	<!-- // modules list -->
	<!-- bottom buttons -->
	<nav class="btngroup">
		<span><a href="<?=ROOT?>/module/index/" class="ui-button">목록</a></span>
		<span><a href="<?=ROOT?>/group/index/" class="ui-button">모듈그룹</a></span>
		<span><a href="<?=ROOT?>/module/create/" class="ui-button btn-highlight">모듈만들기</a></span>
	</nav>
	<!-- // bottom buttons -->
</section>