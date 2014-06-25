<?
$path = ROOT.'/plugins/editor/'.$nest['editor'];

$article['content'] = htmlspecialchars($article['content']);
$article['content'] = nl2br($article['content']);
?>

<link rel="stylesheet" href="<?=$path?>/css/basic.css" />