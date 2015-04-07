<?php
if(!defined("GOOSE")){exit();}

// set nest name
$nestName = ($nest['srl']) ? '['.$nest['name'].'] ' : '';

// set where by article
if ($nest_srl)
{
	$where = 'nest_srl='.$nest_srl;
	$where .= ($category_srl) ? ' and category_srl='.$category_srl : '';
}

// set article count
$articleCount = $goose->spawn->getCount(array(
	'table' => 'articles',
	'where' => ($where) ? $where : ''
));

// set list type
$listType = (isset($nest['json']['listType'])) ? $nest['json']['listType'] : $listTypes[1];
?>

<link rel="stylesheet" href="<?=GOOSE_ROOT?><?=$path_skin?>/assets/style.css" />

<section>
	<div class="hgroup">
		<?
		$url = GOOSE_ROOT.'/article/index/';
		$url .= ($nest_srl) ? $nest_srl.'/' : '';
		?>
		<h1><a href="<?=$url?>"><?=$nestName?>문서목록</a></h1>
	</div>

	<?
	if ($nest['useCategory'] == 1)
	{
		$categories = $goose->spawn->getItems(array(
			'table' => 'categories',
			'where' => 'nest_srl='.$nest_srl,
			'order' => 'turn',
			'sort' => 'asc'
		));
	?>
		<nav class="goose-categories">
			<ul>
				<?
				$active = (!$category_srl) ? 'class="active"' : '';
				$cnt = $goose->spawn->getCount(array(
					'table' => 'articles',
					'where' => 'nest_srl='.$nest_srl
				));
				?>
				<li <?=$active?>>
					<a href="<?=GOOSE_ROOT?>/article/index/<?=$nest_srl?>/">All(<?=($cnt)?>)</a>
				</li>
				<?
				foreach($categories as $k=>$v)
				{
					$cnt = $goose->spawn->getCount(array(
						'table' => 'articles',
						'where' => 'nest_srl='.$nest_srl.' and category_srl='.$v['srl']
					));
					$active = ($category_srl == $v['srl']) ? " class='active'" : "";
					?>
					<li<?=$active?>>
						<a href="<?=GOOSE_ROOT?>/article/index/<?=$nest_srl?>/<?=$v['srl']?>/"><?=$v['name']?>(<?=$cnt?>)</a>
					</li>
					<?
				}
				?>
			</ul>
		</nav>
	<?
	}
	?>

	<ul class="goose-index <?=$listType?>">
		<?
		if ($articleCount > 0)
		{
			require_once(PWD.'/libs/Paginate.class.php');
			$paginateParameter = array('keyword'=>(isset($_GET['keyword']))?$_GET['keyword']:'');
			$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
			$paginate = new Paginate($articleCount, $_GET['page'], $paginateParameter, $nest['listCount'], 5);
			$no = $paginate->no;

			$article = $goose->spawn->getItems(array(
				'field' => '*',
				'table' => 'articles',
				'where' => $where,
				'order' => 'srl',
				'sort' => 'desc',
				'limit' => array($paginate->offset, $paginate->size)
			));

			foreach ($article as $k=>$v)
			{
				$url = GOOSE_ROOT.'/article/view/';
				$url .= ($category_srl) ? $category_srl.'/' : '';
				$url .= $v['srl'].'/';
				$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
				$categoryName = ($v['category_srl']) ? $goose->spawn->getItem(array(
					'table' => 'categories',
					'where' => 'srl='.$v['category_srl']
				)) : '';
				$categoryName = (isset($categoryName['name'])) ? '<span>분류:'.$categoryName['name'].'</span> ' : '';
				$v['json'] = json_decode(urldecode($v['json']), true);
		?>
				<li>
					<a href="<?=$url?>">
						<dl>
							<dt>
								<?=($v['json']['thumnail']['url']) ? '<img src="'.GOOSE_ROOT.$dataThumnailDirectory.$v['json']['thumnail']['url'].'" alt=""/>' : '<div class="noimg">noimg</div>'?>
							</dt>
							<dd>
								<strong><?=$v['title']?></strong>
								<div class="inf">
									<?=$categoryName?>
									<span>조회수:<?=$v['hit']?></span>
									<span>작성날짜:<?=$goose->util->convertDate($v['regdate'])?></span>
								</div>
							</dd>
						</dl>
					</a>
				</li>
		<?
				$no = $no - 1;
			}
		}
		else
		{
			echo "<li class=\"empty\">데이터가 없습니다.</li>";
		}
		?>
	</ul>

	<!-- bottom navigation -->
	<div class="controller">
		<dl>
			<dt><?=($articleCount > 0) ? $paginate->createNavigation() : '';?></dt>
			<dd>
				<nav class="btngroup">
					<?
					$url = GOOSE_ROOT.'/article/create/';
					$url .= ($nest_srl) ? $nest_srl.'/' : '';
					$url .= ($category_srl) ? $category_srl.'/' : '';
					?>
					<span><a href="<?=$url?>" class="ui-button btn-highlight">글쓰기</a></span>
					<?
					$url = GOOSE_ROOT.'/nest/index/';
					$url .= ($_SESSION['group_srl']) ? $_SESSION['group_srl'].'/' : '';
					?>
					<span><a href="<?=$url?>" class="ui-button">둥지목록</a></span>
					<?
					if (count($categories) > 0)
					{
						$url = GOOSE_ROOT.'/category/index/';
						$url .= ($nest_srl) ? $nest_srl.'/' : '';
						echo "<span><a href='$url' class='ui-button'>분류목록</a></span>";
					}
					?>
				</nav>
			</dd>
		</dl>
	</div>
	<!-- // bottom navigation -->
</section>