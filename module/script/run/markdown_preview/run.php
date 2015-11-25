<?php
if (!defined('__GOOSE__')) exit();

$post = Util::getMethod();

require_once(__GOOSE_PWD__.'/vendor/Parsedown/Parsedown.class.php');
require_once(__GOOSE_PWD__.'/vendor/parsedown-extra/ParsedownExtra.php');

$parsedown = new ParsedownExtra();

if ($post['content'])
{
	echo '<div class="markdown-body">'.$parsedown->text($post['content']).'</div>';
}
else
{
	echo '<div class="markdown-error">[ERROR] 내용이 없습니다.</div>';
}

Goose::end(false);