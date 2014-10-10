<?php
if(!defined("GOOSE")){exit();}

$group_srl = (isset($_SESSION['group_srl'])) ? (int)$_SESSION['group_srl'] : null;
$group_srl = (isset($routePapameters['param0'])) ? (int)$routePapameters['param0'] : $group_srl;
$group_srl = ($_GET['all']) ? null : $group_srl;

$_SESSION['group_srl'] = $group_srl;

$itemParameter = ($group_srl) ? 'group_srl='.$group_srl : '';
$nestsAllCount = $goose->spawn->getCount(array('table'=>'nests'));
$nestsCount = $goose->spawn->getCount(array('table'=>'nests', 'where'=>$itemParameter));
$nestsIndex = $goose->spawn->getItems(array(
	'table' => 'nests',
	'where' => $itemParameter,
	'order' => 'srl',
	'sort' => 'desc'
));
$nestGroupsCount = $goose->spawn->getCount(array('table'=>'nestGroups'));
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=GOOSE_ROOT?>/nest/index/">Nests</a></h1>
	</div>
	<?
	if ($nestGroupsCount)
	{
	?>
		<!-- groups list -->
		<nav class="goose-categories">
			<ul>
				<?
				$active = (!$group_srl) ? 'class="active"' : '';
				?>
				<li <?=$active?>>
					<a href="<?=GOOSE_ROOT?>/nest/index/?all=1">All(<?=($nestsAllCount)?>)</a>
				</li>
				<?
				$nestGroupsIndex = $goose->spawn->getItems(array(
					'table' => 'nestGroups',
					'order' => 'srl',
					'sort' => 'desc'
				));
				foreach ($nestGroupsIndex as $k=>$v)
				{
					$nestCount = $goose->spawn->getCount(array(
						'table' => 'nests',
						'where' => 'group_srl='.(int)$v['srl']
					));
					$active = ($group_srl == $v['srl']) ? " class='active'" : "";
					$url = GOOSE_ROOT.'/nest/index/'.$v['srl'].'/';
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
	<ul class="goose-index">
		<?
		if ($nestsCount > 0)
		{
			foreach ($nestsIndex as $k=>$v)
			{
				$v['json'] = json_decode(urldecode($v['json']));
				$url = GOOSE_ROOT.'/article/index/'.$v['srl'].'/';
				$articleCount = $goose->spawn->getCount(array(
					'table' => 'articles',
					'where' => 'nest_srl='.(int)$v['srl']
				));
				$categoryCount = $goose->spawn->getCount(array(
					'table' => 'categories',
					'where' => 'nest_srl='.(int)$v['srl']
				));
				$categoryCount = ($categoryCount && $v['useCategory']==1) ? '<span>분류:'.$categoryCount.'</span>' : '';
				$groupName = $goose->spawn->getItem(array(
					'field' => 'name',
					'table' => 'nestGroups',
					'where' => 'srl='.(int)$v['group_srl']
				));
				$groupName = ($groupName['name']) ? "<em>[".$groupName['name']."]</em>" : "";
				$categoryBtn = ($v['useCategory'] == 1) ? '<a href="'.GOOSE_ROOT.'/category/index/'.$v['srl'].'/">분류설정</a>' : '';
		?>
				<li>
					<div class="body">
						<a href="<?=$url?>">
							<strong><?=$groupName?> <?=$v['name']?>(<?=$articleCount?>)</strong>
						</a>
						<div class="inf">
							<span>아이디:<?=$v['id']?></span>
							<span>날짜:<?=$goose->util->convertDate($v['regdate'])?></span>
							<?=$categoryCount?>
							<span>썸네일사이즈:<?=$v['thumnailSize']?></span>
							<span>Article:<?=$v['json']->articleSkin?></span>
						</div>
						<nav>
							<a href="<?=GOOSE_ROOT?>/nest/modify/<?=$v['srl']?>/">수정</a>
							<a href="<?=GOOSE_ROOT?>/nest/delete/<?=$v['srl']?>/">삭제</a>
							<?=$categoryBtn?>
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
		<span><a href="<?=GOOSE_ROOT?>/nest/index/" class="ui-button">목록</a></span>
		<span><a href="<?=GOOSE_ROOT?>/group/index/" class="ui-button">둥지그룹</a></span>
		<span><a href="<?=GOOSE_ROOT?>/nest/create/" class="ui-button btn-highlight">둥지만들기</a></span>
	</nav>
	<!-- // bottom buttons -->
</section>