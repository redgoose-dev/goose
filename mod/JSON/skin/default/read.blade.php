@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $repo->json['name'],
		'description' => 'regdate: ' . core\Util::convertDate($repo->json['regdate']) . ' ' . core\Util::convertTime($repo->json['regdate']),
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ],
		'titleType' => $mod->name
	])

	<div class="json-code">
		<pre class="js" id="jsonData"></pre>
	</div>

	<nav class="gs-btn-group right">
		<a href="{{ $root }}/{{ $mod->name }}/index/" class="gs-button">목록</a>
		<a href="{{ $root }}/{{ $mod->name }}/modify/{{ $json_srl }}/" class="gs-button col-key">수정</a>
		<a href="{{ $root }}/{{ $mod->name }}/remove/{{ $json_srl }}/" class="gs-button">삭제</a>
	</nav>
</section>
@endsection

@section('style')
<link type="text/css" rel="stylesheet" href="{{ $root }}/vendor/snippet/jquery.snippet.min.css"/>
@endsection

@section('script')
<script src="{{ $root }}/vendor/snippet/jquery.snippet.min.js"></script>
<script>
$(function () {
	var json = '{{ $repo->json['json'] }}';
	json = (json) ? JSON.parse(decodeURIComponent(json.replace(/\+/g, '%20'))) : '';

	$('#jsonData').html(JSON.stringify(json, null, 2));
	$('pre.js').snippet('javascript', {
		style: 'bright',
		menu: true,
		showNum: false
	});
});
</script>
@endsection