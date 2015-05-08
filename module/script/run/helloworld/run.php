<?php
if (!defined('__GOOSE__')) exit();
?>

<section>
	<h1>Hello world</h1>
	<p>테스트 스크립트입니다. 무슨 기능이든 작성하여 사용하세요~</p>

	<h3>$meta is</h3>
	<p><?php var_dump($meta);?></p>

	<hr/>

	<h3>$this->goose is</h3>
	<p><?php var_dump($this->goose);?></p>
</section>

<?php
return Array('print' => 'complete script');
?>