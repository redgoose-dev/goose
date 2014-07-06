<?
$path = ROOT.'/plugins/editor/'.$nest['editor'];

require_once('lib/Parsedown.php');
$Parsedown = new Parsedown();
$article['content'] = '<div class="markdown-body">'.$Parsedown->text($article['content']).'</div>';
?>

<link rel="stylesheet" href="<?=$path?>/css/view.css" />