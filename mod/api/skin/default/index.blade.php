@extends($layout->skinAddr.'.layout')

@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => false, 'setting' => true ]
	])

	<h2>Token</h2>
	<p>
		토큰은 헤더에서 `TOKEN`키값으로 사용하거나 url 파라메터에서 `&token={TOKEN}`으로 사용할 수 있습니다.
	</p>

	<h2>Help</h2>
	<p>
		API 모듈에 사용에 관한 내용은 <a href="#">API 도움말</a>페이지를 참고하시길 바랍니다.
	</p>
</section>
@endsection