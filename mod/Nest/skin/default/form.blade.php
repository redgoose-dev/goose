@extends($layout->skinAddr.'.layout')


@section('content')
@include($mod->name . '.skin.default.form-data')
@include($mod->name . '.skin.default.form-lib')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description']
	])

	<form action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post" id="regster-form">
		<input type="hidden" name="json" value="" />
		<input type="hidden" name="nest_srl" value="{{ $mod->nest_srl }}" />

		<fieldset class="form-group">
			@yield('nestSkin')

			@yield('articleSkin')

			@yield('app')

			@yield('id')

			@yield('nestName')

			@yield('listCount')

			@yield('useCategory')

			@yield('permission')
		</fieldset>

		@yield('buttons')
	</form>
</section>
@endsection

@section('script')
<script src="{{ $root }}/vendor/validation/jquery.validate.min.js"></script>
<script src="{{ $root }}/vendor/validation/localization/messages_ko.js"></script>
<script>
jQuery(function($){
	// change skin
	$('#nestSkin').on('change', function(){
		var url = location.origin + location.pathname;
		url += ($(this).val()) ? '?skin=' + $(this).val() : '';
		location.href = url;
	});

	// check validate
	$.validator.addMethod("alphanumeric", function(value, element) {
		return this.optional(element) || /^[a-zA-Z0-9_-]+$/.test(value);
	});
	$('#regster-form').validate({
		rules : {
			id : {required : true, minlength : 2, alphanumeric : true}
			,name : {required: true, minlength: 2}
			,listCount : {required: true, number: true}
		}
		,messages : {
			id : {alphanumeric: '알파벳과 숫자만 사용가능합니다.'}
		}
		,submitHandler : function(form) {
			var json = {
				nestSkin : form.nestSkin.value,
				articleSkin : form.articleSkin.value,
				listCount : (form.listCount) ? parseInt(form.listCount.value) : 15,
				useCategory : parseInt($(form.useCategory).filter(':checked').val()),
				permission : parseInt(form.permission.value),
				permission2 : parseInt(form.permission2.value)
			};
			form.json.value = encodeURIComponent(JSON.stringify(json));
			form.submit();
			return false;
		}
	});
});
</script>
@endsection