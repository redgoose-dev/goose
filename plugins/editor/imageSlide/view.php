<?
$path = ROOT.'/plugins/editor/'.$nest['editor'];

$article['content'] = urldecode($article['content']);
$article['content'] = json_decode($article['content']);
?>

<link rel="stylesheet" href="<?=$path?>/css/view.css" />

<div class="articleBody">
	<ul>
		<?
		foreach ($article['content'] as $k=>$v)
		{
		?>
			<li>
				<div class="wrap">
					<figure><img src="<?=ROOT?>/data/original/<?=$v->location?>" alt="" /></figure>
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
</div>