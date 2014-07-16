<?
$path = ROOT.'/plugins/editor/'.$nest['editor'];

$article['content'] = urldecode($article['content']);
$article['content'] = json_decode($article['content']);

$util->console($article['content']);
?>

<div class="articleBody">
	<?
	var_dump($article['content']);
	?>
</div>