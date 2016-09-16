@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<ul class="idx-document {{ $mod->set['listStyle'] or 'list' }}">
		@if(count($repo->user))
			@foreach($repo->user as $user)
				@if($mod->isAdmin || ($_SESSION['goose_email'] == $user['email']))
				<li>
					<div class="wrap">
						<div class="body">
							<strong class="hd">{{ $user['name'] }}</strong>
							<div class="inf">
								<span><b>Email</b>{{ $user['email'] }}</span>
								<span><b>Date</b>{{ core\Util::convertDate($user['regdate']) }}</span>
								<span><b>Level</b>{{ $user['level'] }}</span>
							</div>
							<nav>
								<a href="{{ $root }}/{{ $mod->name }}/modify/{{ $user['srl'] }}/">수정</a>
								@if($mod->isAdmin)
								<a href="{{ $root }}/{{ $mod->name }}/remove/{{ $user['srl'] }}/">삭제</a>
								@endif
							</nav>
						</div>
					</div>
				</li>
				@endif
			@endforeach
		@else
		<li class="empty">데이터가 없습니다.</li>
		@endif
	</ul>

	<nav class="gs-btn-group right">
		<a href="{{ $root }}/{{ $mod->name }}/index/" class="gs-button">목록</a>
		@if($mod->isAdmin)
		<a href="{{ $root }}/{{ $mod->name }}/create/" class="gs-button col-key">사용자 등록</a>
		@endif
	</nav>
</section>
@endsection