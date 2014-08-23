<?php
if(!defined("GOOSE")){exit();}

if ($nest_srl)
{
	if ($nest['srl'])
	{
		$nestName = '['.$nest['name'].'] ';
		$category = $goose->spawn->getItems(array(
			'table' => 'categories',
			'where' => 'nest_srl='.$nest['srl'],
			'order' => 'turn',
			'sort' => 'asc'
		));
	}
	else
	{
		$goose->util->back('없는 둥지번호입니다.');
		exit;
	}

	$where = 'nest_srl='.$nest_srl;
	$where .= ($category_srl) ? ' and category_srl='.$category_srl : '';
}

$articleCount = $goose->spawn->getCount(array(
	'table' => 'articles',
	'where' => ($where) ? $where : ''
));


// init paginate
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
}
?>

<link rel="stylesheet" href="<?=GOOSE_ROOT?><?=$path_skin?>/style.css" />

<section>
	<div class="hgroup">
		<?
		$url = GOOSE_ROOT.'/article/index/';
		$url .= ($nest_srl) ? $nest_srl.'/' : '';
		?>
		<h1><a href="<?=$url?>"><?=$nestName?>문서목록</a></h1>
	</div>

	<?
	if (count($category) > 0)
	{
	?>
		<nav class="goose-categories">
			<ul>
				<?
				foreach($category as $k=>$v)
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

	<ul class="goose-index">
		<?
		if ($articleCount > 0)
		{
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
				$img = ($v['thumnail_url']) ? '<dt><img src="'.GOOSE_ROOT.'/data/thumnail/'.$v['thumnail_url'].'" alt=""/></dt>' : '';
				$noimg = ($v['thumnail_url']) ? "class=\"noimg\"" : "";
				$json = json_decode($v['json'], true);
				$tags = $json['tag'];
		?>
				<li>
					<a href="<?=$url?>">
						<dl <?=$noimg?>>
							<?=$img?>
							<dd class="body">
								<strong><?=$v['title']?></strong>
								<div class="inf">
									<?=$categoryName?>
									<span>조회수:<?=$v['hit']?></span>
									<span>작성날짜:<?=$goose->util->convertDate($v['regdate'])?></span>
								</div>
								<?
								if (count($tags))
								{
								?>
									<div class="tagList">
										<ul>
											<?
											foreach ($tags as $k2=>$v2)
											{
												echo "<li>$v2</li>";
											}
											?>
										</ul>
									</div>
								<?
								}
								?>
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
					if (count($category) > 0)
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