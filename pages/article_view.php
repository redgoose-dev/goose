<?php
if(!defined("GOOSE")){exit();}

$article_srl = (int)$routePapameters['param0'];
$categoryName = null;
$_GET['page'] = (isset($_GET['page'])) ? $_GET['page'] : null;
$_GET['m'] = (isset($_GET['m'])) ? $_GET['m'] : null;

if (!$article_srl)
{
	$util->back('srl값이 없습니다.');
}

$article = $spawn->getItem(array(
	'table' => $tablesName['articles'],
	'where' => 'srl='.$article_srl
));

// get nest
$nest = $spawn->getItem(array(
	'table' => $tablesName['nests'],
	'where' => 'srl='.(int)$article['nest_srl']
));

// get category
if ($article['category_srl'])
{
	$category = $spawn->getItem(array(
		'table' => $tablesName['categories'],
		'where' => 'srl='.(int)$article['category_srl']
	));
	$categoryName = ($nest['useCategory']) ? "<span class=\"category\">[$category[name]]</span>&nbsp;" : "";
}
?>

<section>
	<div class="hgroup">
		<h1><?=$categoryName.$article['title']?></h1>
		<p>
			<span><?=$util->convertDate($article['regdate']).'&nbsp;'.$util->convertTime($article['regdate'])?></span>
			<span>HIT:<?=$article['hit']?></span>
		</p>
	</div>
	<?
	// get extra vars count
	$extraVarsCount = $spawn->getCount(array(
		'table' => $tablesName['extraVar'],
		'where' => 'article_srl='.(int)$article['srl']
	));

	$extraVarCount = $spawn->getCount(array(
		'table' => $tablesName['extraVar'],
		'where' => 'article_srl='.(int)$article['srl']
	));
	
	if ($extraVarsCount > 0)
	{
		$extraKeys = $spawn->getItems(array(
			'table' => $tablesName['extraKey'],
			'where' => 'nest_srl='.(int)$article['nest_srl'],
			'order' => 'turn',
			'sort' => 'asc',
		));
	?>
		<!-- Extra var -->
		<section class="extraContents">
			<h1>확장변수</h1>
			<div class="body">
				<?
				foreach($extraKeys as $k=>$v)
				{
					$extraVar = $spawn->getItem(array(
						'table' => $tablesName['extraVar'],
						'where' => 'article_srl='.(int)$article['srl'].' and key_srl='.(int)$v['srl']
					));
					if ($extraVar['value'])
					{
						$extraVar['value'] = nl2br($extraVar['value']);
				?>
						<dl>
							<dt><?=$v['name']?></dt>
							<dd><?=$extraVar['value']?></dd>
						</dl>
				<?
					}
				}
				?>
			</div>
		</section>
		<!-- // Extra var -->
	<?
	}
	?>

	<!-- body -->
	<?
	// Import editor Plugin
	$editorDir = PWD.'/plugins/editor/';
	if (file_exists($editorDir.$nest['editor'].'/view.php'))
	{
		require_once($editorDir.$nest['editor'].'/view.php');
	}
	else
	{
		if (file_exists($editorDir.'basic/view.php'))
		{
			require_once($editorDir.'basic/view.php');
		}
	}
	?>
	<!-- // body -->

	<hr />

	<!-- bottom navigation -->
	<nav class="btngroup">
		<?
		if ($_GET['m'])
		{
			$url = GOOSE_ROOT.'/';
		}
		else
		{
			$url = GOOSE_ROOT.'/article/index/';
			$url .= ($article['nest_srl']) ? $article['nest_srl'].'/' : '';
			$url .= ($article['category_srl']) ? $article['category_srl'].'/' : '';
			$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
		}
		?>
		<span><a href="<?=$url?>" class="ui-button">목록</a></span>
		<?
		$url = GOOSE_ROOT.'/article/create/';
		$url .= ($nest['srl']) ? $nest['srl'].'/' : '';
		$url .= ($article['category_srl']) ? $article['category_srl'].'/' : '';
		$url .= ($_GET['m']) ? '?m='.$_GET['m'] : '';
		?>
		<span><a href="<?=$url?>" class="ui-button">글쓰기</a></span>
		<?
		$url = GOOSE_ROOT.'/article/modify/';
		$url .= ($article_srl) ? $article_srl.'/' : '';
		$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
		$url .= ($_GET['m']) ? '?m='.$_GET['m'] : '';
		?>
		<span><a href="<?=$url?>" class="ui-button btn-highlight">수정</a></span>
		<?
		$url = GOOSE_ROOT.'/article/delete/';
		$url .= ($article_srl) ? $article_srl.'/' : '';
		$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
		$url .= ($_GET['m']) ? '?m='.$_GET['m'] : '';
		?>
		<span><a href="<?=$url?>" id="docDelete" class="ui-button">삭제</a></span>
	</nav>
	<!-- // bottom navigation -->
</section>
