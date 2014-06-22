<?php
if(!defined("GOOSE")){exit();}

// get article count
$articlesCount = $spawn->getCount(array(
	table => $tablesName[articles]
));
$articlesIndex = $spawn->getItems(array(
	table => $tablesName[articles],
	order => 'srl',
	sort => 'desc',
	limit => array(0, 20)
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
				$nest = $spawn->getItem(array(
					field => 'name,useCategory',
					table => $tablesName[nests],
					where => 'srl='.$v[nest_srl]
				));
				if ($nest[useCategory] && $v[category_srl])
				{
					$categoryName = $spawn->getItem(array(
						field => 'name',
						table => $tablesName[categories],
						where => 'srl='.$v[category_srl]
					));
					$categoryName = ($categoryName) ? "<span>분류:$categoryName[name]</span>" : "";
				}
				$url = ROOT.'/article/view/'.$v[srl].'/?m=1';
				$img = ($v[thumnail_url]) ? "<dt><img src=\"".ROOT."/data/thumnail/$v[thumnail_url]\" alt=\"$v[title]\" /></dt>" : "";
				$noimg = ($v[thumnail_url]) ? "class=\"noimg\"" : "";
				echo "
					<li>
						<a href=\"$url\">
							<dl $noimg>
								$img
								<dd class=\"body\">
									<strong><em>[$nest[name]]</em> $v[title]</strong>
									<div class=\"inf\">
										$categoryName
										<span>작성날짜:".$util->convertDate($v[regdate])."</span>
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
