<!doctype html>
<html>
<head lang="ko">
	<meta charset="utf-8">
	<title>Goose admin</title>
	<meta name="author" content="redgoose">
	<meta name="generator" content="PHPStorm">
	<meta name="description" content="admin service for goose">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no">
	<link rel="stylesheet" href="{{ $root }}/vendor/material-design-icons/material-icons.css" />
	<link rel="stylesheet" href="{{ $root }}/{{ $layout->skinPath }}css/layout.css" media="screen" />
	@yield('style')
</head>
<body>
<main role="main">
	<!-- Header -->
	<header id="header">
		<h1><a href="{{ $root }}/">Goose</a></h1>
		@if(count($layout->navigation))
		<nav id="gnb">
			<ul>
				@foreach($layout->navigation as $item)
				<li><a href="{{ $item['url'] }}"{!! ($item['target']) ? ' target="'.$item['target'].'"' : '' !!}>{{ $item['name'] }}</a></li>
				@endforeach
			</ul>
		</nav>
		@endif
	</header>
	<!-- // Header -->

	<!-- Container -->
	<div id="container">
		@yield('content')
	</div>
	<!-- // Container -->

	<!-- Footer -->
	<footer id="footer">
		<address>Copyright {{ date("Y") }} Goose Engine. All right reserved.</address>
	</footer>
	<!-- // Footer -->
</main>
@yield('popup')
<script src="{{ $root }}/vendor/jQuery/jquery-3.1.x.min.js"></script>
@yield('script')
</body>
</html>