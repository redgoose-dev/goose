<?php
if (!defined('__GOOSE__')) exit();

$this->route->map('GET', '/', 'intro');
$this->route->map('GET|POST', '/[a:module]/', 'module');
$this->route->map('GET|POST', '/[a:module]/[a:action]/', 'module');
$this->route->map('GET|POST', '/[a:module]/[a:action]/[*:param0]/[*:param1]/[*:param2]/[*:param3]/', 'module');
$this->route->map('GET|POST', '/[a:module]/[a:action]/[*:param0]/[*:param1]/[*:param2]/', 'module');
$this->route->map('GET|POST', '/[a:module]/[a:action]/[*:param0]/[*:param1]/', 'module');
$this->route->map('GET|POST', '/[a:module]/[a:action]/[*:param0]/', 'module');