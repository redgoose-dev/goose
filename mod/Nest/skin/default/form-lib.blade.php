{{-- nest skin --}}
@section('nestSkin')
<dl class="gs-webz">
	<dt><label for="nestSkin">Nest Skin</label></dt>
	<dd>
		<select name="nestSkin" id="nestSkin">
			@if(!count($repo->skins))
				<option value="">Empty skin</option>
			@endif
			@foreach($repo->skins as $skin)
				<?php $selected = ($skin == $nowSkin) ? ' selected' : '' ?>
				<?php $selected = (!$nowSkin && $skin=='default') ? ' selected' : $selected ?>
				<option value="{{ $skin }}"{{ $selected }}>{{ $skin }}</option>
			@endforeach
		</select>
		<p class="gs-color-red">{{ $nestSkinMessage or '스킨을 바꾸면 페이지가 이동합니다.' }}</p>
	</dd>
</dl>
@endsection

{{-- article skin --}}
@section('articleSkin')
<dl class="gs-webz">
	<dt><label for="articleSkin">Article Skin</label></dt>
	<dd>
		@set( $articleSkin = $repo->nest['json']['articleSkin'] )
		@set( $articleDefaultSkin = $article->set['skin'] )
		<select name="articleSkin" id="articleSkin">
			@if(!count($repo->articleSkins))
				<option value="default">Empty skin</option>
			@endif
			@foreach($repo->articleSkins as $skin)
				<?php $selected = ($skin == $articleSkin) ? ' selected' : '' ?>
				<?php $selected = (!$articleSkin && ($skin == $articleDefaultSkin)) ? ' selected' : $selected ?>
				<option value="{{ $skin }}"{{ $selected }}>{{ $skin }}</option>
			@endforeach
		</select>
	</dd>
</dl>
@endsection

{{-- app --}}
@section('app')
@if(count($repo->apps) && $mod->isAdmin)
<dl class="gs-webz">
	<dt><label for="app_srl">App</label></dt>
	<dd>
		<select name="app_srl" id="app_srl" {{ (!$mod->isAdmin) ? 'disabled' : '' }}>
			<option value="0">선택하세요.</option>
			@foreach($repo->apps as $app)
				<?php $selected = ($app['srl'] == $repo->nest['app_srl']) ? ' selected' : '' ?>
				<?php $selected = ((!$repo->nest['app_srl']) && ($app['srl']==$_SESSION['app_srl'])) ? ' selected' : $selected ?>
				<option value="{{ $app['srl'] }}"{{ $selected }}>{{ $app['name'] }}</option>
			@endforeach
		</select>
	</dd>
</dl>
@else
<input type="hidden" name="app_srl" value="{{ $repo->nest['app_srl'] }}">
@endif
@endsection

{{-- id --}}
@section('id')
<dl class="gs-webz">
	<dt><label for="id">ID</label></dt>
	<dd>
		<input
			type="text" name="id" id="id" maxlength="20" size="18"
			placeholder="영문과 숫자 입력가능"
			value="{{ ($repo->nest['id'] && $action == 'modify') ? $repo->nest['id'] : '' }}" />
	</dd>
</dl>
@endsection

{{-- nest name --}}
@section('nestName')
<dl class="gs-webz">
	<dt><label for="name">둥지이름</label></dt>
	<dd>
		<input type="text" name="name" id="name" maxlength="100" size="22" value="{{ $repo->nest['name'] }}"/>
	</dd>
</dl>
@endsection

{{-- list count --}}
@section('listCount')
<dl class="gs-webz">
	<dt><label for="listCount">목록수</label></dt>
	<dd>
		<input
			type="tel" name="listCount" id="listCount"
			maxlength="3" size="4"
			value="{{ $repo->nest['json']['listCount'] or $mod->set['countArticle'] }}"/>
		<p>한페이지에 출력되는 글 갯수입니다.</p>
	</dd>
</dl>
@endsection

{{-- use category --}}
@section('useCategory')
<dl class="gs-webz">
	<dt><label for="useCategory">분류사용</label></dt>
	<dd>
		<label>
			<input
				type="radio" name="useCategory" id="useCategory" value="0"
				{{ $useCategory['no'] or 'checked' }}>
			<span>사용안함</span>
		</label>
		<label>
			<input type="radio" name="useCategory" value="1" {{ $useCategory['yes'] }}>
			<span>사용</span>
		</label>
	</dd>
</dl>
@endsection

{{-- permission --}}
@section('permission')
<dl class="gs-webz">
	<dt><label for="permission">권한설정</label></dt>
	<dd>
		<label>
			<span>읽기권한 : </span>
			<input
				type="tel" name="permission" id="permission"
				maxlength="4" size="5"
				value="{{ $repo->nest['json']['permission'] or $mod->set['permission'] }}"
				{{ (!$mod->isAdmin) ? 'readonly' : '' }}>
		</label>
		<label>
			<span>쓰기권한 : </span>
			<input
				type="tel" name="permission2"
				maxlength="4" size="5"
				value="{{ $repo->nest['json']['permission2'] or $mod->set['adminPermission'] }}"
				{{ (!$mod->isAdmin) ? 'readonly' : '' }}>
		</label>
	</dd>
</dl>
@endsection


{{-- buttons --}}
@section('buttons')
<nav class="gs-btn-group right">
	<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
	<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
</nav>
@endsection
