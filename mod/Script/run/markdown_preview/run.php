<?php
if (!defined('__GOOSE__')) exit();

/**
 * @var string $path
 * @var array $meta
 */

// get $_POST
$post = core\Util::getMethod();

// load and init Parsedown
require_once(__GOOSE_PWD__ . '/vendor/parsedown/Parsedown.php');
$parsedown = new Parsedown();

// print content
if ($post['content'])
{
	echo '<div class="markdown-body">' . $parsedown->text($post['content']) . '</div>';
}
else
{
	echo '<div class="markdown-error">[ERROR] 내용이 없습니다.</div>';
}