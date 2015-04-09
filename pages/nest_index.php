<?php
if(!defined("GOOSE")){exit();}
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=GOOSE_ROOT?>/nest/index/">Nests</a></h1>
	</div>

	<!-- groups list -->
	<nav class="goose-categories">
		<ul>
			<?
			$active = (!$group_srl) ? 'class="active"' : '';
			?>
			<li <?=$active?>>
				<a href="<?=GOOSE_ROOT?>/nest/index/?all=1">All(<?=$nestsAllCount?>)</a>
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
				$nestCount = '('.$nestCount.')';

				$active = ($group_srl == $v['srl']) ? " class='active'" : "";
				$url = GOOSE_ROOT.'/nest/index/'.$v['srl'].'/';
				echo "<li $active><a href=\"$url\">$v[name] $nestCount</a></li>";
			}
			?>
		</ul>
	</nav>
	<!-- // groups list -->

	<!-- nests list -->
	<ul class="goose-index list">
		<?
		if ($nestsCount > 0)
		{
			foreach ($nestsIndex as $k=>$v)
			{
				$v['json'] = json_decode(urldecode($v['json']), true);
				$allow = ($v['json']['permission'] && $v['json']['permission'] < $_SESSION['gooseLevel']) ? false : true;
				if ($allow)
				{
					$url = GOOSE_ROOT.'/article/index/'.$v['srl'].'/';
					$articleCount = $goose->spawn->getCount(array(
						'table' => 'articles',
						'where' => 'nest_srl='.(int)$v['srl']
					));
					$categoryCount = $goose->spawn->getCount(array(
						'table' => 'categories',
						'where' => 'nest_srl='.(int)$v['srl']
					));
					$categoryCount = ($categoryCount && $v['useCategory']==1) ? '<span>Category:'.$categoryCount.'</span>' : '';
					$groupName = $goose->spawn->getItem(array(
						'field' => 'name',
						'table' => 'nestGroups',
						'where' => 'srl='.(int)$v['group_srl']
					));
					$groupName = ($groupName['name']) ? "<em>[".$groupName['name']."]</em>" : "";
					$categoryBtn = ($v['useCategory'] == 1) ? '<a href="'.GOOSE_ROOT.'/category/index/'.$v['srl'].'/">분류설정</a>' : '';
		?>
					<li>
						<dl>
							<dd>
								<a href="<?=$url?>"><strong class="big"><?=$groupName?> <?=$v['name']?>(<?=$articleCount?>)</strong></a>
								<div class="inf">
									<span>ID:<?=$v['id']?></span>
									<span>Date:<?=$goose->util->convertDate($v['regdate'])?></span>
									<?=$categoryCount?>
									<span>Level:<?=$v['json']['permission']?></span>
								</div>
								<?
								if ($goose->isAdmin)
								{
								?>
									<nav>
										<a href="<?=GOOSE_ROOT?>/nest/modify/<?=$v['srl']?>/">수정</a>
										<a href="<?=GOOSE_ROOT?>/nest/delete/<?=$v['srl']?>/">삭제</a>
										<?=$categoryBtn?>
									</nav>
								<?
								}
								?>
							</dd>
						</dl>
					</li>
		<?
				}
				else
				{
		?>
					<li>
						<dl>
							<dd>
								<strong class="disabled">권한이 없는 둥지</strong>
							</dd>
						</dl>
					</li>
		<?
				}
			}
		}
		else
		{
		?>
			<li class="empty">데이터가 없습니다.</li>;
		<?
		}
		?>
	</ul>
	<!-- // nests list -->

	<!-- bottom buttons -->
	<nav class="btngroup">
		<span><a href="<?=GOOSE_ROOT?>/nest/index/" class="ui-button">목록</a></span>
		<?
		if ($goose->isAdmin)
		{
		?>
			<span><a href="<?= GOOSE_ROOT ?>/group/index/" class="ui-button">둥지그룹</a></span>
			<span><a href="<?= GOOSE_ROOT ?>/nest/create/" class="ui-button btn-highlight">둥지만들기</a></span>
		<?
		}
		?>
	</nav>
	<!-- // bottom buttons -->
</section>