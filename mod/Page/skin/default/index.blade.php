@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<ul class="idx-document list">
		@if(count($repo->pages))
		@foreach($repo->pages as $page)
		<li>
			<a href="{{ $page['url'] }}" class="wrap">
				<div class="body">
					<strong class="hd">
						<span>{{ $page['filename'] }}</span>
					</strong>
					<div class="inf">
						<span><b>Path</b>{{ $page['url'] }}</span>
					</div>
				</div>
			</a>
		</li>
		@endforeach
		@else
		<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>
</section>
@endsection