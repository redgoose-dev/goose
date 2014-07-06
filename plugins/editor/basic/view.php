<?
$path = ROOT.'/plugins/editor/'.$nest['editor'];

$article['content'] = htmlspecialchars($article['content']);
$article['content'] = nl2br($article['content']);

$article['content'] = str_replace('[[', '<', $article['content']);
$article['content'] = str_replace(']]', '>', $article['content']);
?>

<link rel="stylesheet" href="<?=$path?>/css/basic.css" />