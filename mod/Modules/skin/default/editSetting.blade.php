@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => '[' . $mod->name . '] 모듈 환경설정',
		'description' => $mod->set['description']
	])

	<form action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post" name="post" id="regsterForm">
		<input type="hidden" name="module" value="{{ $repo->setting['name'] }}"/>
		<input type="hidden" name="json" value="{{ core\Util::arrayToJson($repo->setting, true) }}"/>
		<input type="hidden" name="referer" value="{{ $_SERVER['HTTP_REFERER'] }}">

		<fieldset class="form-group">
			<legend class="blind">edit setting</legend>
			<dl>
				<dt><label>JSON DATA</label></dt>
				<dd>
					<div class="JSONEditor" id="JSONEditor"></div>
				</dd>
			</dl>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">수정하기</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
		</nav>
	</form>
</section>
@endsection

@section('style')
<link rel="stylesheet" href="{{ $root }}/vendor/JSONEditor/dist/css/JSONEditor.css" media="screen" />
@endsection

@section('script')
<script src="{{ $root }}/vendor/JSONEditor/dist/js/JSONEditor.class.js"></script>
<script src="{{ $root }}/vendor/JSONEditor/vendor/jquery-sortable/jquery-sortable.min.js"></script>
<script src="{{ $root }}/vendor/validation/jquery.validate.min.js"></script>
<script src="{{ $root }}/vendor/validation/localization/messages_ko.js"></script>
<script>
jQuery(function($){
	var $jsonEditor = $('#JSONEditor');
	var $form = $('#regsterForm');
	var jsonData = $form.get(0).json.value;
	var jsonEditor = new JSONEditor($jsonEditor);

	// import json
	var json;
	try {
		json = JSON.parse(decodeURIComponent(jsonData.replace(/\+/g, '%20')));
	}
	catch(e) {
		json = {};
	}
	jsonEditor.replace(json);

	// submit form
	$form.on('submit', function(){
		$(this).find('input[name=json]').val(jsonEditor.export(false));
	});

	// value validate
	$form.validate({
		ignore: '[contenteditable]',
		rules : {
			name : {required : true, minlength : 3}
		}
	});
});
</script>
@endsection