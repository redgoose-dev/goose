@extends($layout->skinAddr.'.layout')


@section('content')
@include($mod->name . '.skin.default.index-data')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	@if(count($repo->apps))
	<!-- apps list -->
	<nav class="idx-category">
		<ul>
			<li{!! (!$app_srl) ? ' class="active"' : '' !!}>
				<a href="{{ $root }}/{{ $mod->name }}/index/{{ ($_GET['skin']) ? '?skin='.$_GET['skin'] : '' }}">
					<span>All</span>
					<em class="gs-brk-cnt">{{ $repo->total }}</em>
				</a>
			</li>
			@foreach($repo->apps as $app)
			{{ $active = ($app_srl == $app['srl']) ? ' class="active"' : '' }}
			<li{!! $active !!}>
				<a href="{{ $root }}/{{ $mod->name }}/index/{{ $app['srl'] }}/{{ ($_GET['skin']) ? '?skin='.$_GET['skin'] : '' }}">
					<span>{{ $app['name'] }}</span>
					<em class='gs-brk-cnt'>{{ $app['countNest'] }}</em>
				</a>
			</li>
			@endforeach
		</ul>
	</nav>
	<!-- // apps list -->
	@endif

	<!-- index -->
	<ul class="idx-document list">
		@if(count($repo->nests))
			@foreach($repo->nests as $nest)
				@if($_SESSION['goose_level'] < $nest['json']['permission'])
				<li class=\"permission-denied\">permission denied</li>
				@else
				<li>
					<div class="wrap">
						<div class="body">
							<a href="{{ $root }}/Article/index/{{ $nest['srl'] }}/">
								<strong class="hd">
									{{ $appName }}
									{{ $nest['name'] }}
									<em class="gs-brk-cnt">{{ $nest['countArticle'] }}</em>
								</strong>
							</a>
							<div class="inf">
								<span><b>ID</b>{{ $nest['id'] }}</span>
								<span><b>Date</b>{{ core\Util::convertDate($nest['regdate']) }}</span>
								@if($nest['json']['useCategory'] == 1)
								<span><b>Category</b>{{ $nest['countCategory'] }}</span>
								@endif
								<span><b>Nest skin</b>{{ $nest['json']['nestSkin'] }}</span>
								<span><b>Article skin</b>{{ $nest['json']['articleSkin'] }}</span>
							</div>
							@if($mod->isAdmin || ($_SESSION['goose_level'] > $nest['json']['permission2']))
							<nav>
								<a href="{{ $root }}/{{ $mod->name }}/modify/{{ $nest['srl'] }}/">수정</a>
								<a href="{{ $root }}/{{ $mod->name }}/remove/{{ $nest['srl'] }}/">삭제</a>
								@if($nest['json']['useCategory'] == 1)
									<a href="{{ $root }}/Category/index/{{ $nest['srl'] }}/">분류설정</a>
								@endif
								@if($mod->isAdmin)
									<a href="{{ $root }}/{{ $mod->name }}/clone/{{ $nest['srl'] }}/">복제</a>
								@endif
							</nav>
							@endif
						</div>
					</div>
				</li>
				@endif
			@endforeach
		@else
		<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>
	<!-- // index -->

	<!-- buttons -->
	<nav class="gs-btn-group right">
		<a href="{{ $root }}/{{ $mod->name }}/" class="gs-button">목록</a>
		@if($mod->isAdmin)
			<a href="{{ $root }}/App/" class="gs-button">APP목록</a>
			<a href="{{ $root }}/{{ $mod->name }}/create/" class="gs-button col-key">둥지만들기</a>
		@endif
	</nav>
	<!-- // buttons -->
</section>
@endsection