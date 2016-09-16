@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<?php $admin = ($mod->isAdmin || ($mod->set['adminPermission'] <= $_SESSION['goose_level'])) ?>

	<ul class="idx-document list">
		@if(count($repo->json))
			@foreach($repo->json as $json)
			<li>
				<div class="wrap">
					<div class="body">
						<a href="{{ $root }}/{{ $mod->name }}/read/{{ $json['srl'] }}/">
							<strong class="hd">{{ $json['name'] }}</strong>
						</a>
						<div class="inf">
							<span><b>Date</b>{{ core\Util::convertDate($json['regdate']) }}</span>
						</div>
						@if($admin)
						<nav>
							<a href="{{ $root }}/{{ $mod->name }}/modify/{{ $json['srl'] }}/">수정</a>
							<a href="{{ $root }}/{{ $mod->name }}/remove/{{ $json['srl'] }}/">삭제</a>
						</nav>
						@endif
					</div>
				</div>
			</li>
			@endforeach
		@else
			<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>

	<nav class="gs-btn-group right">
		<a href="{{ $root }}/{{ $mod->name }}/index/" class="gs-button">목록</a>
		@if($admin)
		<a href="{{ $root }}/{{ $mod->name }}/create/" class="gs-button col-key">JSON만들기</a>
		@endif
	</nav>
</section>
@endsection