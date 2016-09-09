@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<ul class="idx-document {{ $mod->set['listStyle'] or 'list' }}">
		@if(count($repo->run))
		@foreach($repo->run as $run)
		<li>
			<div class="wrap">
				<div class="body">
					<strong class="hd">{{ ($run['meta']['name']) ? $run['meta']['name'] : $run['name'] }}</strong>
					@if($run['meta']['description'])
						<p class="description">{{ $run['meta']['description'] }}</p>
					@endif
					<nav>
						<a href="{{ $root }}/{{ $mod->name }}/run/{{ $run['name'] }}/">실행하기</a>
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