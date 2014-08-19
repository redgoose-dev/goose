<?
$path = GOOSE_ROOT.'/plugins/editor/'.$nest['editor'];
$extPath = GOOSE_ROOT.'/libs/ext';

require_once(PWD.'/libs/ext/Parsedown/Parsedown.class.php');
$Parsedown = new Parsedown();
$article['content'] = '<div class="markdown-body">'.$Parsedown->text($article['content']).'</div>';

$json = json_decode($article['json']);
$tags = $json->tag;
?>

<link rel="stylesheet" href="<?=$extPath?>/Parsedown/markdown.css" />
<link rel="stylesheet" href="<?=$path?>/markdown.css" />

<div class="articleBody">
	<?=$article['content']?>

	<?
	if (count($tags))
	{
	?>
		<section class="tagList">
			<h1>TAGS</h1>
			<ul>
				<?
				foreach ($tags as $k=>$v)
				{
					echo "<li>$v</li>";
				}
				?>
			</ul>
		</section>
	<?
	}
	?>
</div>