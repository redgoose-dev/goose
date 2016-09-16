@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<ul class="idx-document card">
	@if(count($repo->file))
		@foreach($repo->file as $file)
		<li>
			<a class="wrap" href="{{ $root }}/{{ $file['loc'] }}" target="_blank">
				<figure class="figure">
					@if($file['loc'] && (preg_match("/^image/", $file['type'])))
					<img src="{{ $root }}/{{ $file['loc'] }}" alt="{{ $file['name'] }}"/>
					@else
					<span class="noimg">noimg</span>
					@endif
				</figure>
				<div class="body">
					<strong class="hd">{{ $file['name'] }}</strong>
					<div class="inf">
						<span><b>Date</b>{{ core\Util::convertDate($file['regdate']) }}</span>
						<span><b>Type</b>{{ $file['type'] }}</span>
						<span><b>Size</b>{{ core\Util::getFileSize($file['size']) }}</span>
					</div>
				</div>
			</a>
		</li>
		@endforeach
	@else
		<li class="empty">데이터가 없습니다.</li>
	@endif
	</ul>

	<dl class="gs-webz top">
		@if(count($repo->file))
			<dt>{!! $repo->paginate->createNavigation() !!}</dt>
		@endif
		<dd>
			<nav class="gs-btn-group right">
				<a href="{{ $root }}/{{ $mod->name }}/index/" class="gs-button">목록</a>
			</nav>
		</dd>
	</dl>
</section>
@endsection