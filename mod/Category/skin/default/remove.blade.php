@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'titleType' => $repo->nest['name'],
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form action="{{ $root }}/Category/{{ $mod->params['action'] }}/" method="post" id="regsterForm">
		<input type="hidden" name="nest_srl" value="{{ $nest_srl }}"/>
		<input type="hidden" name="category_srl" value="{{ $category_srl }}"/>

		<fieldset class="form-group">
			<p class="message"><em class="gs-brk-quot">{{ $repo->category['name'] }}</em> 분류를 삭제하시겠습니까? 삭제된 분류는 복구할 수 없습니다.</p>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">삭제하기</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">돌아가기</button>
		</nav>
	</form>
</section>
@endsection