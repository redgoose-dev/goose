@extends($layout->skinAddr.'.layout')


@section('content-body')
<div class="gs-article-body">
	{!! nl2br(htmlspecialchars($repo->article['content'])) !!}
</div>
@endsection

@section('files')
@if(count($repo->file))
<article class="attach-files box">
	<h1>Attach files</h1>
	<ul>
		@foreach($repo->file as $file)
		<li>
			<a href="{{ $root }}/{{ $file['loc'] }}" target="_blank">
				<span>{{ $file['name'] }}</span>
				<em class="gs-brk-cnt">{{ core\Util::getFileSize($file['size']) }}</em>
			</a>
		</li>
		@endforeach
	</ul>
</article>
@endif
@endsection

@section('buttons')
<nav class="gs-btn-group right">
	<?php
	$param_index = [
		'type' => 'index',
		'nest_srl' => $repo->nest['srl'],
		'category_srl' => $category_srl,
		'page' => ($_GET['page'] > 1) ? $_GET['page'] : '',
		'main' => $_GET['m']
	];
	?>
	<a href="{{ $mod->createLinkUrlInReadPage($param_index) }}" class="gs-button">목록</a>
	@if($mod->isAdmin)
	<?php
	$param_create = [
		'type' => 'create',
		'nest_srl' => $repo->nest['srl'],
		'category_srl' => $category_srl,
		'main' => $_GET['m']
	];
	$param_modify = [
		'type' => 'modify',
		'category_srl' => $category_srl,
		'article_srl' => $article_srl,
		'page' => ($_GET['page'] > 1) ? $_GET['page'] : '',
		'main' => $_GET['m']
	];
	$param_remove = [
		'type' => 'remove',
		'category_srl' => $category_srl,
		'article_srl' => $article_srl,
		'page' => ($_GET['page'] > 1) ? $_GET['page'] : '',
		'main' => $_GET['m']
	];
	?>
	<a href="{{ $mod->createLinkUrlInReadPage($param_create) }}" class="gs-button">글쓰기</a>
	<a href="{{ $mod->createLinkUrlInReadPage($param_modify) }}" class="gs-button col-key">수정</a>
	<a href="{{ $mod->createLinkUrlInReadPage($param_remove) }}" class="gs-button">삭제</a>
	@endif
</nav>
@endsection


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $repo->article['title'],
		'description' => '<span>' . core\Util::convertDate($repo->article['regdate']) . ' ' . core\Util::convertTime($repo->article['regdate']) . '</span>' . '<span>hit:' . $repo->article['hit'],
		'isHeadNavigation' => true,
		'titleType' => $repo->category['name']
	])

	@yield('content-body')

	@yield('files')

	<hr />

	@yield('buttons')
</section>
@endsection