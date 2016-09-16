@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<ul class="idx-document list">
		@if(count($repo->modules))
			@foreach($repo->modules as $module)
			<li>
				<div class="body">
					<strong class="hd">[{{ $module['set']['name'] }}] {{ ($module['set']['title']) ? $module['set']['title'] : '' }}</strong>
					@if($module['set']['description'])
					<p class="description">{{ $module['set']['description'] }}</p>
					@endif
					<div class="inf">
						@if(in_array($module['set']['name'], $installModules))
						<span><b>Install</b>설치됨</span>
						@endif
						@if($module['set']['permission'])
						<span><b>접근권한 레벨</b>$set[permission]</span>
						@endif
						@if($module['set']['adminPermission'])
						<span><b>관리자권한 레벨</b>{{ $module['set']['adminPermission'] }}</span>
						@endif
						@if($module['set']['skin'])
						<span><b>Skin</b>{{ $module['set']['skin'] }}</span>
						@endif
					</div>
					<nav>
						<?php $dir_helpFile = $pwd . 'mod/' . $module['name'] . '/help/index' ?>
						@if(file_exists($dir_helpFile . '.html') || file_exists($dir_helpFile . '.md'))
						<a href="{{ $root }}/Help/{{ $module['set']['name'] }}/">도움말</a>
						@endif
						<a href="{{ $root }}/{{ $mod->name }}/editSetting/{{ $module['set']['name'] }}/">설정</a>
						@if($module['set']['install'] && !in_array($module['set']['name'], $installModules))
						<a href="{{ $root }}/{{ $mod->name }}/install/{{ $module['set']['name'] }}/" data-action='confirm' data-message='설치하시겠습니까?'>설치</a>
						@elseif($module['set']['install'] && in_array($module['set']['name'], $installModules) && !in_array($module['set']['name'], $systemModules) && core\Module::existMethod($module['set']['name'], 'uninstall'))
						<a href="{{ $root }}/{{ $mod->name }}/uninstall/{{ $module['set']['name'] }}/" data-action='confirm' data-message='정말 설치해제하시겠습니까?'>설치해제</a>
						@endif
					</nav>
				</div>
			</li>
			@endforeach
		@else
			<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>
</section>
@endsection

@section('script')
<script>
jQuery(function($){
	// confirm event
	$('[data-action=confirm]').on('click', function(){
		return (confirm($(this).data('message'))) ? true : false;
	});
});
</script>
@endsection