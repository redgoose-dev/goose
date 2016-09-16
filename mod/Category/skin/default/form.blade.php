@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'titleType' => $repo->nest['name'],
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<form action="{{ $root }}/Category/{{ $mod->params['action'] }}/" method="post" id="regsterForm">
		<input type="hidden" name="nest_srl" value="{{ $nest_srl }}"/>
		<input type="hidden" name="category_srl" value="{{ $category_srl }}"/>

		<fieldset class="form-group">
			<dl class="gs-webz first">
				<dt><label for="name">이름</label></dt>
				<dd><input type="text" name="name" id="name" size="20" maxlength="20" value="{{ $repo->category['name'] }}" required/></dd>
			</dl>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">돌아가기</button>
		</nav>
	</form>
</section>
@endsection

@section('script')
<script src="{{ $root }}/vendor/validation/jquery.validate.min.js"></script>
<script src="{{ $root }}/vendor/validation/localization/messages_ko.js"></script>
<script>
jQuery('#regsterForm').validate();
</script>
@endsection