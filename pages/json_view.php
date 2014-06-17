<?php
if(!defined("GOOSE")){exit();}

$json_srl = (int)$routePapameters['param0'];

if (!$json_srl)
{
	$util->back('srl값이 없습니다.');
}

$json = $spawn->getItem(array(
	table => $tablesName[jsons],
	where => 'srl='.$json_srl
));
?>

<section>
	<div class="hgroup">
		<h1><?=$json[srl]?>. <?=$json[name]?></h1>
		<p><?=$util->convertDate($json[regdate]).'&nbsp;'.$util->convertTime($json[regdate])?></p>
	</div>

	<!-- body -->
	<div class="jsonCode">
		<pre class="js" id="jsonData"></pre>
	</div>
	<!-- // body -->

	<!-- bottom navigation -->
	<nav class="btngroup">
		<span><a href="<?=ROOT?>/json/index/" class="ui-button">목록</a></span>
		<span><a href="<?=ROOT?>/json/modify/<?=$json[srl]?>/" class="ui-button btn-highlight">수정</a></span>
		<span><a href="<?=ROOT?>/json/delete/<?=$json[srl]?>/" class="ui-button">삭제</a></span>
	</nav>
</section>

<link type="text/css" rel="stylesheet" href="<?=ROOT?>/pages/src/pkg/snippet/jquery.snippet.min.css"/>
<script type="text/javascript" src="<?=$jQueryAddress?>"></script>
<script type="text/javascript" src="<?=ROOT?>/pages/src/pkg/snippet/jquery.snippet.min.js"></script>
<script>
$(function(){
	$.browser={};(function(){jQuery.browser.msie=false;
	$.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
	$.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();

	$('#jsonData').html(JSON.stringify(JSON.parse('<?=$json[json]?>'), null, 5));
	$('pre.js').snippet('javascript', {style:'bright', menu:true, showNum:true});
});
</script>