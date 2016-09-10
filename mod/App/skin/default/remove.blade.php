@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post" id="regsterForm">
		<input type="hidden" name="app_srl" value="{{ $app_srl }}"/>

		<fieldset class="form-group">
			<p class="message"><em class="gs-brk-quot">{{ $repo->app['name'] }}</em> App을 삭제하시겠습니까? 삭제된 App은 복구할 수 없습니다.</p>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">삭제하기</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">돌아가기</button>
		</nav>
	</form>
</section>
@endsection