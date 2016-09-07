@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'titleType' => $repo->nest['name'],
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form action="{{ $root }}/Category/sort/" method="post" name="listForm" id="listForm">
		<input type="hidden" name="nest_srl" value="{{ $nest_srl }}" />
		<input type="hidden" name="srls" value=""/>
		<fieldset>
			<legend class="blind">분류목록</legend>
			<ul class="idx-document sortable card" id="index">
			@if(count($repo->category))
			@foreach($repo->category as $category)
				<li class="item" data-srl="{{ $category['srl'] }}">
					<div class="wrap">
						<div class="handle">
							<i class="material-icons">dehaze</i>
						</div>
						<div class="body">
							<strong class="hd">{{ $category['srl'] }}. {{ $category['name'] }}({{ $category['articleCount'] }})</strong>
							@if($mod->isAdmin || ($permission < $_SESSION['goose_level']))
							<nav>
								<a href="{{ $root }}/Category/modify/{{ $category['urlParam'] }}">수정</a>
								<a href="{{ $root }}/Category/remove/{{ $category['urlParam'] }}">삭제</a>
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
		</fieldset>

		<nav class="gs-btn-group right">
			<?php $param = ($nest_srl) ? $nest_srl . '/' : '' ?>
			@if($mod->isAdmin || ($permission < $_SESSION['goose_level']))
			<a href="{{ $root }}/Category/create/{{ $param }}" class="gs-button col-key">분류추가</a>
			@endif
			<a href="{{ $root }}/Article/index/{{ $param }}" class="gs-button">문서목록</a>
			<a href="{{ $root }}/Nest/index/{{ ($_SESSION['app_srl']) ? $_SESSION['app_srl'].'/' : '' }}" class="gs-button">둥지목록</a>
		</nav>
	</form>
</section>
@endsection

@section('script')
<script src="{{ $root }}/vendor/Sortable/Sortable.min.js"></script>
<script>
jQuery(function($) {
	var $index = $('#index');
	var srls = $index.children('li').map(function(){
		return $(this).data('srl')
	}).get().join(',');

	// init sortable
	var sortable = Sortable.create($index.get(0), {
		animation : 200,
		handle : '.handle',
		draggable : '.item',
		onUpdate : function(e) {
			var newSrls = $index.children('li').map(function(){
				return $(this).data('srl')
			}).get().join(',');

			if (srls == newSrls) return false;

			// set srls
			srls = newSrls;

			$.post('{{ $root }}/Category/sort/', {
				nest_srl : '{{ $nest_srl }}',
				srls : srls,
				ajax : true
			}).done(function(res){
				try {
					res = JSON.parse(res);
					if (res.state !== 'success') alert('update fail');
				} catch(e) {}
			});
		}
	});
});
</script>
@endsection