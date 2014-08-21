<?php
if(!defined("GOOSE")){exit();}

/*
$nest_srl = (int)$routePapameters['param0'];
$category_srl = (isset($routePapameters['param1'])) ? (int)$routePapameters['param1'] : null;
*/

if ($nest_srl)
{
	$nest = $spawn->getItem(array(
		'table' => $tablesName['nests'],
		'where' => 'srl='.$nest_srl
	));
	if ($nest['srl'])
	{
		$nestName = '['.$nest['name'].'] ';
		$category = $spawn->getItems(array(
			'table' => $tablesName['categories'],
			'where' => 'nest_srl='.(int)$nest['srl'],
			'order' => 'turn',
			'sort' => 'asc'
		));
	}
	else
	{
		$util->back('없는 둥지번호입니다.');
		exit;
	}

	$articleWhere = 'nest_srl='.$nest_srl;
	$articleWhere .= ($category_srl) ? ' and category_srl='.$category_srl : '';
}

$articleCount = $spawn->getCount(array(
	'table' => $tablesName['articles'],
	'where' => $articleWhere
));

// init paginate
if ($articleCount > 0)
{
	require_once(PWD.'/libs/Paginate.class.php');

	$paginateParameter = array('keyword'=>(isset($_GET['keyword']))?$_GET['keyword']:'');
	$_GET['page'] = ((isset($_GET['page'])) && $_GET['page'] > 1) ? $_GET['page'] : 1;
	$paginate = new Paginate($articleCount, $_GET['page'], $paginateParameter, $nest['listCount'], 5);
	$no = $paginate->no;

	$article = $spawn->getItems(array(
		'field' => '*',
		'table' => $tablesName['articles'],
		'where' => $articleWhere,
		'order' => 'srl',
		'sort' => 'desc',
		'limit' => array($paginate->offset, $paginate->size)
	));
}
?>

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
		<nav class="categories">
			<ul>
				<?
				foreach($category as $k=>$v)
				{
					$cnt = $spawn->getCount(array(
						'table' => $tablesName['articles'],
						'where' => 'category_srl='.$v['srl']
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

	<!-- index -->
	<?
	// Import editor Plugin
	$editorDir = PWD.'/plugins/editor/';
	if (file_exists($editorDir.$nest['editor'].'/index.php'))
	{
		require_once($editorDir.$nest['editor'].'/index.php');
	}
	else
	{
	?>
		<ul class="index">
			<?
			if ($articleCount > 0)
			{
				foreach ($article as $k=>$v)
				{
					$url = GOOSE_ROOT.'/article/view/'.$v['srl'].'/';
					$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
					$categoryName = ($v['category_srl']) ? $spawn->getItem(array(
						'table' => $tablesName['categories'],
						'where' => 'srl='.$v['category_srl']
					)) : '';
					$categoryName = (isset($categoryName['name'])) ? '<span>분류:'.$categoryName['name'].'</span> ' : '';
					$img = ($v['thumnail_url']) ? '<dt><img src="'.GOOSE_ROOT.'/data/thumnail/'.$v['thumnail_url'].'" alt=""/></dt>' : '';
					$noimg = ($v['thumnail_url']) ? "class=\"noimg\"" : "";
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
										<span>작성날짜:<?=$util->convertDate($v['regdate'])?></span>
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
	<?
	}
	?>
	<!-- // index -->

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
					$url .= ($nest['group_srl']) ? $nest['group_srl'].'/' : '';
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