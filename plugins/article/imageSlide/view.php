<?php
if(!defined("GOOSE")){exit();}

$extPath = GOOSE_ROOT.'/libs/ext';

if ($nest['useCategory'])
{
	$categoryName = (isset($category['name'])) ? "<span class=\"category\">[$category[name]]</span>&nbsp;" : '';
}

$body = urldecode($article['content']);
$body = json_decode($body);
?>

<link rel="stylesheet" href="<?=GOOSE_ROOT?><?=$path_skin?>/css/view.css" />

<section>
	<div class="hgroup detail">
		<h1><?=$categoryName.$article['title']?></h1>
		<p>
			<span><?=$goose->util->convertDate($article['regdate']).'&nbsp;'.$goose->util->convertTime($article['regdate'])?></span>
			<span>HIT:<?=$article['hit']?></span>
		</p>
	</div>

	<!-- body -->
	<?
	if ($body)
	{
	?>
		<ul class="imagesList">
			<?
			foreach ($body as $k=>$v)
			{
			?>
				<li>
					<div class="wrap">
						<figure><img src="<?=GOOSE_ROOT?>/data/original/<?=$v->location?>" alt="" /></figure>
						<div class="body">
							<h3><?=$v->filename?></h3>
							<?
							foreach ($v->form as $k2=>$v2)
							{
								if($v2->value)
								{
							?>
									<p>
										<strong><?=$v2->key?></strong>
										<span><?=$v2->value?></span>
									</p>
							<?
								}
							}
							?>
						</div>
					</div>
				</li>
			<?
			}
			?>
		</ul>
	<?
	}
	else
	{
		echo "<div class=\"articleBody\">".$article['content']."</div>";
	}
	?>
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