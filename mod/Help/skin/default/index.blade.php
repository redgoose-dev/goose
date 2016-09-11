@extends($layout->skinAddr.'.layout')


@section('content')
	<section>
		@include($layout->skinAddr.'.heading', [
			'title' => $mod->set['title'],
			'description' => $mod->set['description'],
			'isHeadNavigation' => true
		])

		<ul class="idx-document list">
			@if(count($repo->help))
				@foreach($repo->help as $help)
				<li>
					<div class="wrap">
						<div class="body">
							<strong class="hd">[{{ $help['name'] }}] {{ $help['title'] }}</strong>
							<div class="inf">
								<span>{{ $help['description'] }}</span>
							</div>
							<nav>
								<a href="{{ $root }}/{{ $mod->name }}/{{ $help['name'] }}/">도움말</a>
								<a href="{{ $help['url_index'] }}">모듈 바로가기</a>
							</nav>
						</div>
					</div>
				</li>
				@endforeach
			@else
				<li class="empty">데이터가 없습니다.</li>
			@endif
		</ul>

	</section>
@endsection