@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form action="{{ $root }}/App/{{ $action }}/" method="post" id="regsterForm">
		<input type="hidden" name="app_srl" value="{{ $app_srl }}"/>
		<fieldset class="form-group">
			<dl class="gs-webz first">
				<dt><label for="id">아이디</label></dt>
				<dd><input type="text" name="id" id="id" maxlength="30" size="20" value="{{ $repo->app['id'] }}" placeholder="영문과 숫자 입력가능"/></dd>
			</dl>
			<dl class="gs-webz">
				<dt><label for="name">이름</label></dt>
				<dd><input type="text" name="name" id="name" size="20" maxlength="20" value="{{ $repo->app['name'] }}"/></dd>
			</dl>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $titleType }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">돌아가기</button>
		</nav>
	</form>
</section>
@endsection

@section('script')
<script src="{{ $root }}/vendor/validation/jquery.validate.min.js"></script>
<script src="{{ $root }}/vendor/validation/localization/messages_ko.js"></script>
<script>
jQuery(function($){
	// check validate
	$.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9_-]+$/.test(value);
	});
	$('#regsterForm').validate({
		rules : {
			id : {required : true, minlength : 2, alphanumeric : true}
			,name : { required: true, minlength: 2 }
		}
		,messages : {
			id : {alphanumeric: '알파벳과 숫자만 사용가능합니다.'}
		}
	});
});
</script>
@endsection