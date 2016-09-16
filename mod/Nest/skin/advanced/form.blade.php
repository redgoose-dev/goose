@extends($layout->skinAddr.'.layout')


@section('content')
@include($mod->name . '.skin.default.form-data')
@include($mod->name . '.skin.default.form-lib')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
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

			<dl class="gs-webz">
				<dt><label for="thumbWidth">썸네일사이즈</label></dt>
				<dd>
					@set( $size = $repo->nest['json']['thumbnail']['size'] )
					<label>
						<span>가로 :</span>
						<input
							type="number" name="thumbWidth" id="thumbWidth"
							size="3" min="10" max="999"
							value="{{ $size[0] or $mod->set['thumbnailSize'][0] }}"/>
					</label>
					<br/>
					<label>
						<span>세로 :</span>
						<input
							type="number" name="thumbHeight"
							size="3" min="10" max="999"
							value="{{ $size[1] or $mod->set['thumbnailSize'][1] }}"/>
					</label>
				</dd>
			</dl>

			<?php $type = $repo->nest['json']['thumbnail']['type'] ?>
			<dl class="gs-webz">
				<dt><label for="thumbType">썸네일 축소방식</label></dt>
				<dd>
					<label class="dis-ib">
						<input
							type="radio" name="thumbType" id="thumbType"
							value="crop"
							{{ ($type == 'crop' || !isset($type)) ? 'checked' : '' }}>
						<span>자르기</span>
					</label>
					<label class="dis-ib">
						<input
							type="radio" name="thumbType"
							value="resize"
							{{ ($type == 'resize') ? 'checked' : '' }}>
						<span>리사이즈</span>
					</label>
					<label class="dis-ib">
						<input
							type="radio" name="thumbType"
							value="resizeWidth"
							{{ ($type == 'resizeWidth') ? 'checked' : '' }}>
						<span>리사이즈(가로기준)</span>
					</label>
					<label class="dis-ib">
						<input
							type="radio" name="thumbType"
							value="resizeHeight"
							{{ ($type == 'resizeHeight') ? 'checked' : '' }}>
						<span>리사이즈(세로기준)</span>
					</label>
				</dd>
			</dl>

			<dl class="gs-webz">
				<dt><label for="articleListType">List type</label></dt>
				<dd>
					<select name="articleListType" id="articleListType">
						@foreach($mod->set['articleListTypes'] as $type)
							<?php $selected = ($type == $repo->nest['json']['articleListType']) ? ' selected' : '' ?>
							<?php $selected = (!$repo->nest['json']['articleListType'] && $type=='card') ? ' selected' : $selected ?>
							<option value="{{ $type }}"{{ $selected }}>{{ $type }}</option>
						@endforeach
					</select>
					<p>Article 목록에서 출력되는 방식입니다.</p>
				</dd>
			</dl>

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
				thumbnail : {
					size : [parseInt(form.thumbWidth.value), parseInt(form.thumbHeight.value)],
					type : $(form.thumbType).filter(':checked').val()
				},
				articleListType : form.articleListType.value,
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