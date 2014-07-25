<?
$path = GOOSE_ROOT.'/plugins/editor/'.$nest['editor'];
$extPath = GOOSE_ROOT.'/libs/ext';

require_once(PWD.'/libs/ext/Parsedown/Parsedown.class.php');
$Parsedown = new Parsedown();
$article['content'] = '<div class="markdown-body">'.$Parsedown->text($article['content']).'</div>';
?>

<link rel="stylesheet" href="<?=GOOSE_ROOT?>/libs/ext/Parsedown/markdown.css" />

<div class="articleBody">
	<?=$article['content']?>
</div>