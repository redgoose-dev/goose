<?php
if(!defined("GOOSE")){exit();}
?>

<fieldset>
	<dl>
		<dt><label for="content">내용</label></dt>
		<dd><textarea name="content" id="content" rows="20" class="block"><?=htmlspecialchars($article['content'])?></textarea></dd>
	</dl>
</fieldset>