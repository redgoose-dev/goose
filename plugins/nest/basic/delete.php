<?php
if(!defined("GOOSE")){exit();}


$json_string = $goose->util->ArrayToJson($nest['json']);
?>

<section class="goose-form">
	<div class="hgroup">
		<h1>둥지삭제</h1>
	</div>
	<form action="<?=GOOSE_ROOT?>/nest/<?=$paramAction?>/" method="post" id="regsterForm">
		<input type="hidden" name="srl" value="<?=$nest_srl?>" />
		<input type="hidden" name="json" value="<?=$json_string?>" />
		<fieldset>
			<legend class="blind">둥지삭제</legend>
			<p class="message">"<?=$nest['name']?>"둥지를 삭제하시겠습니까? 삭제된 둥지는 복구할 수 없습니다.</p>
		</fieldset>
		<nav class="btngroup">
			<span><button type="submit" class="ui-button btn-highlight">삭제</button></span>
			<span><button type="button" class="ui-button" onclick="history.back(-1)">뒤로가기</button></span>
		</nav>
	</form>
</section>
