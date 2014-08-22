<?php
if(!defined("GOOSE")){exit();}

$extPath = GOOSE_ROOT.'/libs/ext';

require_once(PWD.'/libs/ext/Parsedown/Parsedown.class.php');
$Parsedown = new Parsedown();
$article['content'] = '<div class="markdown-body">'.$Parsedown->text($article['content']).'</div>';

if ($nest['useCategory'])
{
	$categoryName = (isset($category['name'])) ? "<span class=\"category\">[$category[name]]</span>&nbsp;" : '';
}

$tags = (isset($article['json'])) ? $article['json']['tag'] : array();
?>

<link rel="stylesheet" href="<?=$extPath?>/Parsedown/markdown.css" />
<link rel="stylesheet" href="<?=GOOSE_ROOT?><?=$path_skin?>/style.css" />

<section>
	<div class="hgroup detail">
		<h1><?=$categoryName.$article['title']?></h1>
		<p>
			<span><?=$goose->util->convertDate($article['regdate']).'&nbsp;'.$goose->util->convertTime($article['regdate'])?></span>
			<span>HIT:<?=$article['hit']?></span>
		</p>
	</div>

	<!-- body -->
	<div class="articleBody">
		<?=$article['content']?>
	
		<?
		if (count($tags))
		{
		?>
			<section class="tagList">
				<h1>TAGS</h1>
				<ul>
					<?
					foreach ($tags as $k=>$v)
					{
						echo "<li>$v</li>";
					}
					?>
				</ul>
			</section>
		<?
		}
		?>
	</div>
	<!-- body -->

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
			$url .= ($nest['srl']) ? $nest['srl'].'/' : '';
			$url .= ($category_srl) ? $category_srl.'/' : '';
			$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
		}
		?>
		<span><a href="<?=$url?>" class="ui-button">목록</a></span>
		<?
		$url = GOOSE_ROOT.'/article/create/';
		$url .= ($nest['srl']) ? $nest['srl'].'/' : '';
		$url .= ($category_srl) ? $category_srl.'/' : '';
		$url .= ($_GET['m']) ? '?m='.$_GET['m'] : '';
		?>
		<span><a href="<?=$url?>" class="ui-button">글쓰기</a></span>
		<?
		$url = GOOSE_ROOT.'/article/modify/';
		$url .= ($category_srl) ? $category_srl.'/' : '';
		$url .= ($article_srl) ? $article_srl.'/' : '';
		$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
		$url .= ($_GET['m']) ? '?m='.$_GET['m'] : '';
		?>
		<span><a href="<?=$url?>" class="ui-button btn-highlight">수정</a></span>
		<?
		$url = GOOSE_ROOT.'/article/delete/';
		$url .= ($category_srl) ? $category_srl.'/' : '';
		$url .= ($article_srl) ? $article_srl.'/' : '';
		$url .= ($_GET['page'] > 1) ? '?page='.$_GET['page'] : '';
		$url .= ($_GET['m']) ? '?m='.$_GET['m'] : '';
		?>
		<span><a href="<?=$url?>" id="docDelete" class="ui-button">삭제</a></span>
	</nav>
	<!-- // bottom navigation -->
</section>