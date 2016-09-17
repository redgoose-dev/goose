@extends($layout->skinAddr.'.layout')


@section('content')
@include($mod->skinAddr . '.index-data')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<!-- index -->
	<ul class="card-index">
		@foreach($repo->sections as $section)
			@if($_SESSION['goose_level'] >= $nest['json']['permission'])
			<li>
				<strong>{{ $section['name'] }}<em class='gs-brk-cnt'>{{ $section['count'] }}</em></strong>
				@if(count($section['nests']))
				<ul>
					@foreach($section['nests'] as $nest)
					<li>
						<div class="body">
							<a href="{{ $root }}/Article/index/{{ $nest['srl'] }}/">
								<strong class="hd">{{ $nest['name'] }}<em class="gs-brk-cnt">{{ $nest['articleCount'] }}</em></strong>
							</a>
							@if($mod->isAdmin || ($_SESSION['goose_level'] >= $nest['json']['permission2']))
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
					</li>
					@endforeach
				</ul>
				@endif
			</li>
			@endif
		@endforeach
		@if(!$repo->totalNest)
		<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>
	<!-- // index -->

	<!-- buttons -->
	<nav class="gs-btn-group right">
		<a href="{{ $root }}/{{ $mod->name }}/index/" class="gs-button">목록</a>
		@if($mod->isAdmin)
			<a href="{{ $root }}/App/index/" class="gs-button">APP목록</a>
			<a href="{{ $root }}/{{ $mod->name }}/create/" class="gs-button col-key">둥지만들기</a>
		@endif
	</nav>
	<!-- // buttons -->
</section>
@endsection

@section('style')
<link rel="stylesheet" href="{{ $root }}/{{ $mod->skinPath }}css/card.css">
@endsection