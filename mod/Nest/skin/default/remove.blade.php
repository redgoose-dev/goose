@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<form action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post">
		<input type="hidden" name="nest_srl" value="{{ $mod->nest_srl }}"/>
		<input type="hidden" name="nestSkin" value="{{ $repo->nest['json']['nestSkin'] }}"/>
		<input type="hidden" name="app_srl" value="{{ $repo->nest['app_srl'] }}"/>
		<input type="hidden" name="permission2" value="{{ $repo->nest['json']['permission2'] }}"/>

		<fieldset class="form-group">
			<div class="message">
				<p>"{{ $repo->nest['name'] }}" 둥지를 삭제하시겠습니까? 삭제된 둥지는 복구할 수 없습니다.</p>
				<p class="checkboxes">
					<label><input type="checkbox" name="delete_article" checked/><span>article모듈 데이터 삭제</span></label>
				</p>
			</div>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">돌아가기</button>
		</nav>
	</form>
</section>
@endsection