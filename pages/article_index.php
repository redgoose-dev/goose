<?php
if(!defined("GOOSE")){exit();}

$module_srl = (int)$routePapameters['param0'];
$category_srl = (int)$routePapameters['param1'];

if ($module_srl)
{
	$module = $spawn->getItem(array(
		'field' => 'srl,group_srl,name,useCategory,listCount,thumnailSize',
		'table' => $tablesName[modules],
		'where' => 'srl='.$module_srl
	));
	if ($module[srl])
	{
		$moduleName = '['.$module[name].'] ';
		$category = $spawn->getItems(array(
			'table' => $tablesName[categories],
			'where' => 'module_srl='.(int)$module[srl],
			'order' => 'turn',
			'sort' => 'asc'
		));
	}
	else
	{
		$util->back('없는 모듈번호입니다.');
		exit;
	}

	$articleWhere = 'module_srl='.$module_srl;
	$articleWhere .= ($category_srl) ? ' and category_srl='.$category_srl : '';
}

$articleCount = $spawn->getCount(array(
	'table' => $tablesName[articles],
	'where' => $articleWhere
));

// init paginate
if ($articleCount > 0)
{
	require_once(PWD.'/libs/Paginate.class.php');

	$paginateParameter = array('keyword'=>$_GET[keyword]);
	$_GET[page] = ($_GET[page] > 1) ? $_GET[page] : 1;
	$paginate = new Paginate($articleCount, $_GET[page], $paginateParameter, 20, 5);
	$no = $paginate->no;

	$article = $spawn->getItems(array(
		'field' => '*',
		'table' => $tablesName[articles],
		'where' => $articleWhere,
		'order' => 'srl',
		'sort' => 'desc',
		'limit' => array($paginate->offset, $paginate->size)
	));
}
//var_dump($article);
?>

<section>
	<div class="hgroup">
		<?
		$url = ROOT.'/article/index/';
		$url .= ($module_srl) ? $module_srl.'/' : '';
		?>
		<h1><a href="<?=$url?>"><?=$moduleName?>문서목록</a></h1>
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
						'table' => $tablesName[articles],
						'where' => 'category_srl='.$v[srl]
					));
					$active = ($category_srl == $v[srl]) ? " class='active'" : "";
					?>
					<li<?=$active?>>
						<a href="<?=ROOT?>/article/index/<?=$module_srl?>/<?=$v[srl]?>/"><?=$v[name]?>(<?=$cnt?>)</a>
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
	<ul class="index">
		<?
		if ($articleCount > 0)
		{
			foreach ($article as $k=>$v)
			{
				$url = ROOT.'/article/view/'.$v[srl].'/';
				$url .= ($_GET[page] > 1) ? '?page='.$_GET[page] : '';
				$categoryName = ($v[category_srl]) ? $spawn->getItem(array(
					'table' => $tablesName[categories],
					'where' => 'srl='.$v[category_srl]
				)) : '';
				$categoryName = ($categoryName[name]) ? '<span>분류:'.$categoryName[name].'</span> ' : '';
				$img = ($v[thumnail_url]) ? '<dt><img src="'.ROOT.'/data/thumnail/'.$v[thumnail_url].'" alt=""/></dt>' : '';
				$noimg = ($v[thumnail_url]) ? "class=\"noimg\"" : "";
		?>
				<li>
					<a href="<?=$url?>">
						<dl <?=$noimg?>>
							<?=$img?>
							<dd class="body">
								<strong><?=$v[title]?></strong>
								<div class="inf">
									<?=$categoryName?>
									<span>작성날짜:<?=$util->convertDate($v[regdate])?></span>
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
	<!-- // index -->

	<!-- bottom navigation -->
	<div class="controller">
		<dl>
			<dt><?=($articleCount > 0) ? $paginate->createNavigation() : '';?></dt>
			<dd>
				<nav class="btngroup">
					<?
					$url = ROOT.'/article/create/';
					$url .= ($module_srl) ? $module_srl.'/' : '';
					$url .= ($category_srl) ? $category_srl.'/' : '';
					?>
					<span><a href="<?=$url?>" class="ui-button btn-highlight">글쓰기</a></span>
					<?
					$url = ROOT.'/module/index/';
					$url .= ($module[group_srl]) ? $module[group_srl].'/' : '';
					?>
					<span><a href="<?=$url?>" class="ui-button">모듈목록</a></span>
					<?
					if (count($category) > 0)
					{
						$url = ROOT.'/category/index/';
						$url .= ($module_srl) ? $module_srl.'/' : '';
						echo "<span><a href='$url' class='ui-button'>분류목록</a></span>";
					}
					?>
				</nav>
			</dd>
		</dl>
	</div>
	<!-- // bottom navigation -->
</section>