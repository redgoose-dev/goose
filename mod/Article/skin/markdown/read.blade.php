@section('content-body')
<?php
// load and init Parsedown
require_once(__GOOSE_PWD__ . '/vendor/parsedown/parsedown.php');
$parsedown = new Parsedown();
?>
<div class="gs-article-body">
	<div class="markdown-body">{!! $parsedown->text($repo->article['content']) !!}</div>
</div>
@endsection

@section('style')
<link rel="stylesheet" href="{{ $root }}/{{ $mod->skinPath }}css/index.css">
@endsection


@extends('Article.skin.default.read')