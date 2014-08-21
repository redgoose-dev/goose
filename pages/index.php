<?php
if(!defined("GOOSE")){exit();}

// get article count
$articlesCount = $goose->spawn->getCount(array(
	'table' => 'articles'
));
$articlesIndex = $goose->spawn->getItems(array(
	'table' => 'articles',
	'order' => 'srl',
	'sort' => 'desc',
	'limit' => array(0, 20)
));
?>

<section>
	<div class="hgroup">
		<h1>최신글</h1>
	</div>
	<ul class="index">
		<?
		if ($articlesCount > 0)
		{
			foreach ($articlesIndex as $k=>$v)
			{
				$categoryName = null;
				$nest = $goose->spawn->getItem(array(
					'field' => 'name,useCategory',
					'table' => 'nests',
					'where' => 'srl='.$v['nest_srl']
				));
				if ($nest['useCategory'] && $v['category_srl'])
				{
					$categoryName = $goose->spawn->getItem(array(
						'field' => 'name',
						'table' => 'categories',
						'where' => 'srl='.$v['category_srl']
					));
					$categoryName = ($categoryName) ? "<span>분류:$categoryName[name]</span>" : "";
				}
				$url = GOOSE_ROOT.'/article/view/'.$v['srl'].'/?m=1';
				$img = ($v['thumnail_url']) ? "<dt><img src=\"".GOOSE_ROOT."/data/thumnail/$v[thumnail_url]\" alt=\"$v[title]\" /></dt>" : "";
				$noimg = ($v['thumnail_url']) ? "class=\"noimg\"" : "";
				echo "
					<li>
						<a href=\"$url\">
							<dl $noimg>
								$img
								<dd class=\"body\">
									<strong><em>[$nest[name]]</em> $v[title]</strong>
									<div class=\"inf\">
										$categoryName
										<span>조회수:".$v['hit']."</span>
										<span>작성날짜:".$goose->util->convertDate($v['regdate'])."</span>
									</div>
								</dd>
							</dl>
						</a>
					</li>
				";
			}
		}
		else
		{
			echo "<li class=\"empty\">데이터가 없습니다.</li>";
		}
		?>
	</ul>
</section>
