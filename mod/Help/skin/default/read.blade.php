@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' - ' . $repo->setting['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => false
	])

	<div class="gs-article-body">{!! $content !!}</div>

	<hr />

	<nav class="gs-btn-group right">
		<a href="javascript:history.back()" class="gs-button">뒤로가기</a>
	</nav>
</section>
@endsection

@if($fileType == 'md')
@section('style')
<link rel="stylesheet" href="{{ $root }}/vendor/Parsedown/markdown.css"/>
@endsection
@endif