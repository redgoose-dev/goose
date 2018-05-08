@extends($layout->skinAddr.'.layout')

@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => false, 'setting' => true ]
	])

	<p>
		TODO: 정리예정
	</p>

	<h2>token</h2>
	<p>
		토큰은 헤더에서 `TOKEN`키값으로 사용하거나 url 파라메터에서 `&token={TOKEN}`으로 사용할 수 있습니다.
	</p>
</section>
@endsection