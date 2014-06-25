<?
$path = ROOT.'/plugins/editor/'.$nest['editor'];

require_once('lib/Parsedown.php');
$Parsedown = new Parsedown();
$article['content'] = $Parsedown->text($article['content']);
?>

<link rel="stylesheet" href="<?=$path?>/css/markdown.css" />