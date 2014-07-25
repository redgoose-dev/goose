<?php
if(!defined("GOOSE")){exit();}

$jsonCount = $spawn->getCount(array('table'=>$tablesName['jsons']));
$jsonIndex = $spawn->getItems(array(
	'table' => $tablesName['jsons'],
	'order' => 'srl',
	'sort' => 'desc'
));
?>

<section>
	<div class="hgroup">
		<h1><a href="<?=GOOSE_ROOT?>/json/index/">JSON index</a></h1>
	</div>

	<!-- json index -->
	<ul class="index">
		<?
		if ($jsonCount > 0)
		{
			foreach ($jsonIndex as $k=>$v)
			{
			?>
				<li>
					<div class="body">
						<a href="<?=GOOSE_ROOT?>/json/view/<?=$v[srl]?>/">
							<strong><?=$v[srl]?>. <?=$v[name]?></strong>
						</a>
						<div class="inf">
							<span>등록일 : <?=$util->convertDate($v[regdate])?></span>
						</div>
						<nav>
							<a href="<?=GOOSE_ROOT?>/json/modify/<?=$v[srl]?>/">수정</a>
							<a href="<?=GOOSE_ROOT?>/json/delete/<?=$v[srl]?>/">삭제</a>
						</nav>
					</div>
				</li>
			<?
			}
		}
		else
		{
		?>
			<li class="empty">데이터가 없습니다.</li>
		<?
		}
		?>
	</ul>
	<!-- json index -->

	<nav class="btngroup">
		<span><a href="<?=GOOSE_ROOT?>/json/index/" class="ui-button">목록</a></span>
		<span><a href="<?=GOOSE_ROOT?>/json/create/" class="ui-button btn-highlight">JSON만들기</a></span>
	</nav>
</section>