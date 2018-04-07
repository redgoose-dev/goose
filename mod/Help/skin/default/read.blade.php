@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' - ' . $repo->setting['title'],
		'description' => $mod->set['description']
	])

	<div class="gs-article-body">{!! $content !!}</div>

	<hr />

	<nav class="gs-btn-group right">
		<a href="javascript:history.back()" class="gs-button">뒤로가기</a>
	</nav>
</section>
@endsection