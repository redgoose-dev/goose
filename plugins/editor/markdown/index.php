<?
$path = GOOSE_ROOT.'/plugins/editor/'.$nest['editor'];
?>

<link rel="stylesheet" href="<?=$path?>/markdown.css" />

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
								<span>작성날짜:<?=$util->convertDate($v['regdate'])?></span>
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