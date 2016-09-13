@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'titleType' => $repo->nest['name'],
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form name="writeForm" action="{{ $root }}/{{ $mod->name }}/remove/" method="post">
		<input type="hidden" name="article_srl" value="{{ $article_srl }}">
		<input type="hidden" name="nest_srl" value="{{ $repo->article['nest_srl'] }}">
		<input type="hidden" name="category_srl" value="{{ $category_srl }}">
		<input type="hidden" name="skin" value="{{ $repo->nest['json']['articleSkin'] }}">
		<input type="hidden" name="page" value="{{ $_GET['page'] }}" />

		<fieldset class="form-group">
			<legend class="blind">문서{{ $typeName }}</legend>
			<p class="message">`{{ $repo->article['title'] }}` 문서을 삭제하시겠습니까? 삭제된 문서는 복구할 수 없습니다.</p>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">삭제하기</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
		</nav>
	</form>
</section>
@endsection