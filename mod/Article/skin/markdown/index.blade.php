@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'titleType' => $repo->nest['name'],
		'title' => $mod->set['title'],
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	@if(count($repo->category))
		<nav class="idx-category">
			<ul>
				<li{!! (!$category_srl) ? ' class="active"' : '' !!}>
					<a href="{{ $root }}/{{ $mod->name }}/index/{{ ($nest_srl) ? $nest_srl . '/' : '' }}">
						<span>All</span><em>{{ $totalArticleCount }}</em>
					</a>
				</li>
				@foreach($repo->category as $category)
					<li{!! ($category_srl == $category['srl']) ? ' class="active"' : '' !!}>
						<a href="{{ $root }}/{{ $mod->name }}/index/{{ ($nest_srl) ? $nest_srl . '/' : '0/' }}{{ $category['srl'] . '/' }}">
							<span>{{ $category['name'] }}</span><em>{{ $category['countArticle'] }}</em>
						</a>
					</li>
				@endforeach
			</ul>
		</nav>
	@endif

	<ul class="idx-document {{ $repo->nest['json']['articleListType'] or 'list' }}">
		@if(count($repo->article))
			@foreach($repo->article as $article)
			<li>
				<a class="wrap" href="{{ $root }}/{{ $mod->name }}/read/{{ ($category_srl) ? $category_srl . '/' : '' }}{{ $article['srl'] . '/' }}{{ ($_GET['page'] > 1) ? '?page=' . $_GET['page'] : '' }}">
					<figure class="figure">
						@if($article['json']['thumbnail']['url'] && file_exists($pwd . $article['json']['thumbnail']['url']))
						<img src="{{ $root }}/{{ $article['json']['thumbnail']['url'] }}" alt="{{ $article['title'] }}">
						@else
						<span class="noimg">no img</span>
						@endif
					</figure>
					<div class="body">
						<strong class="hd">{{ $article['title'] }}</strong>
						<div class="inf">
							@if($article['categoryName'])
							<span><b>Category</b>{{ $article['categoryName'] }}</span>
							@endif
							<span><b>Hit</b>{{ $article['hit'] }}</span>
							<span><b>Date</b>{{ core\Util::convertDate($article['regdate']) }}</span>
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
		@if(count($repo->article))
		<dt>{!! $repo->paginate->createNavigation() !!}</dt>
		@endif
		<dd>
			<nav class="gs-btn-group right">
				@if($mod->isAdmin)
				<a href="{{ $root }}/{{ $mod->name }}/create/{{ ($nest_srl) ? $nest_srl . '/' : '' }}{{ ($category_srl) ? $category_srl . '/' : '' }}" class="gs-button col-key">글쓰기</a>
				@endif
				<a href="{{ $root }}/Nest/index/{{ ($_SESSION['app_srl']) ? $_SESSION['app_srl'] . '/' : '' }}" class="gs-button">둥지목록</a>
				@if($repo->nest['json']['useCategory'])
				<a href="{{ $root }}/Category/index/{{ ($nest_srl) ? $nest_srl . '/' : '' }}" class="gs-button">분류목록</a>
				@endif
			</nav>
		</dd>
	</dl>
</section>
@endsection