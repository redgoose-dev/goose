@extends($layout->skinAddr.'.layout')


@section('content')
@include($mod->name . '.skin.default.index-data')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => 'Newstest articles',
		'description' => '최근에 올라온 Article 데이터를 출력합니다.'
	])

	<!-- index -->
	<ul class="idx-document card">
		@if(count($repo->articles))
		@foreach($repo->articles as $item)
		@if(isset($item['nest']['json']['permission']) && ($_SESSION['goose_level'] >= (int)$item['nest']['json']['permission']))
		<li>
			<a href="{{ $root }}/Article/read/{{ $item['srl'] }}/?m=1" class="wrap">
				<figure class="figure">
					@if($item['json']['thumbnail']['url'])
					<img src="{{ $root }}/{{ $item['json']['thumbnail']['url'] }}" alt="{{ $item['title'] }}"/>
					@else
					<span class="noimg">no img</span>
					@endif
				</figure>
				<div class="body">
					<strong class="hd">
						@if($item['nest']['name'])
						<em class='gs-brk-type'>{{ $item['nest']['name'] }}</em>
						@endif
						<span>{{ $item['title'] }}</span>
					</strong>
					<div class="inf">
						<?=$categoryName?>
						<span><b>Hit</b>{{ $item['hit'] }}</span>
						<span><b>Date</b>{{ core\Util::convertDate($item['regdate']) }}</span>
					</div>
				</div>
			</a>
		</li>
		@else
		<li class="permission-denied">
			<div class="wrap">
				<span>permission-denied</span>
			</div>
		</li>
		@endif
		@endforeach
		@else
		<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>
	<!-- // index -->
</section>
@endsection