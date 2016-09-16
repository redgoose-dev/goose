@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<form action="{{ $root }}/{{ $mod->name }}/remove/" method="post" id="regsterForm">
		<input type="hidden" name="user_srl" value="{{ $user_srl }}" />

		<fieldset class="form-group">
			<p class="message">`{{ $repo->user['name'] }}`사용자를 삭제하시겠습니까? 삭제된 사용자는 복구할 수 없습니다.</p>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
		</nav>
	</form>
</section>
@endsection