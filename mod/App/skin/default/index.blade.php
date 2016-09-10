@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<ul class="idx-document {{ $mod->set['listStyle'] or 'list' }}">
		@if(count($repo->apps))
			@foreach($repo->apps as $app)
			<li>
				<div class="wrap">
					<div class="body">
						<strong class="hd">
							<span>{{ $app['srl'] }} - {{ $app['name'] }}</span>
							<em class="gs-brk-cnt">{{ $app['nestCount'] }}</em>
						</strong>
						<div class="inf">
							<span><b>ID</b>{{ $app['id'] }}</span>
							<span><b>nest</b>{{ $app['nestCount'] }}</span>
							<span><b>article</b>{{ $app['articleCount'] }}</span>
						</div>
						<nav>
							<a href="{{ $root }}/{{ $mod->name }}/modify/{{ $app['srl'] }}/">수정</a>
							<a href="{{ $root }}/{{ $mod->name }}/remove/{{ $app['srl'] }}/">삭제</a>
						</nav>
					</div>
				</div>
			</li>
			@endforeach
		@else
			<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>

	<nav class="gs-btn-group right">
		<a href="{{ $root }}/{{ $mod->name }}/create/" class="gs-button col-key">APP 만들기</a>
		<a href="{{ $root }}/Nest/index/" class="gs-button">둥지목록</a>
	</nav>
</section>
@endsection