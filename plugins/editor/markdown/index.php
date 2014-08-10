태그추가용 작업준비

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