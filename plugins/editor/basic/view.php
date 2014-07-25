<?
$path = GOOSE_ROOT.'/plugins/editor/'.$nest['editor'];

$article['content'] = htmlspecialchars($article['content']);
$article['content'] = nl2br($article['content']);

$article['content'] = str_replace('[[', '<', $article['content']);
$article['content'] = str_replace(']]', '>', $article['content']);
?>

<style>
.articleBody {
	font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
	font-size: 14px; color: #333; line-height: 1.42857143;
}
</style>

<div class="articleBody">
	<?=$article['content']?>
</div>