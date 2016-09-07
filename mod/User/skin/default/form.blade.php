@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => '사용자 ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post" id="regsterForm">
		<input type="hidden" name="user_srl" value="{{ $user_srl }}" />
		<fieldset class="form-group">
			<legend class="blind">사용자 {{ $typeName }}</legend>
			<dl class="gs-webz first">
				<dt><label for="email">이메일 주소</label></dt>
				<dd>
					<input
						type="email" name="email" id="email"
						size="30" maxlength="40"
						value="{{ $repo->user['email'] }}"
						placeholder="name@domain.com"
						{{ ($repo->user['email']) ? 'readonly' : '' }}>
				</dd>
			</dl>
			<dl class="gs-webz">
				<dt><label for="name">닉네임</label></dt>
				<dd>
					<input
						type="text" name="name" id="name"
						size="20" maxlength="20"
						placeholder="닉네임을 입력하세요."
						value="{{ $repo->user['name'] }}">
				</dd>
			</dl>
			<dl class="gs-webz">
				<dt><label for="pw">비밀번호</label></dt>
				<dd>
					<input
						type="password" name="pw" id="pw"
						size="15" maxlength="20"
						{{ ($mod->params['action']=='create') ? 'required' : '' }}>
				</dd>
			</dl>
			<dl class="gs-webz">
				<dt><label for="pw2">비밀번호 확인</label></dt>
				<dd><input type="password" name="pw2" id="pw2" size="15" maxlength="20" /></dd>
			</dl>
			@if($mod->isAdmin)
			<dl class="gs-webz">
				<dt><label for="level">권한</label></dt>
				<dd>
					<input
						type="tel" name="level" id="level"
						size="4" maxlength="4"
						value="{{ $repo->user['level'] }}" placeholder="10"/>
					<p>
						접근레벨: {{ $mod->set['permission'] }},
						관리자레벨: {{ $mod->set['adminPermission'] }}
					</p>
				</dd>
			</dl>
			@elseif($repo->user['level'])
			<input type="hidden" name="level" value="{{ $repo->user['level'] }}">
			@endif
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
		</nav>
	</form>
</section>
@endsection

@section('script')
<script src="{{ $root }}/vendor/validation/jquery.validate.min.js"></script>
<script src="{{ $root }}/vendor/validation/localization/messages_ko.js"></script>
<script>
jQuery(function($){
	$('#regsterForm').validate({
		rules : {
			name : { required: true, minlength: 2 }
			,email : { required: true }
			,pw : { minlength: 3 }
			,pw2 : { equalTo: '#pw' }
			,level : { required: true, number: true }
		}
	});
});
</script>
@endsection