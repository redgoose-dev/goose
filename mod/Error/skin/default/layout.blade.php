<!DOCTYPE html>
<html>
<head lang="ko">
	<meta charset="UTF-8">
	<title>Goose error</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" user-scalable="no">
	<link rel="stylesheet" href="{{ $root }}/vendor/material-design-icons/material-icons.css" />
	<link rel="stylesheet" href="{{ $root }}/{{ $mod->skinPath }}/css/render.css"/>
	@yield('style')
</head>
<body>
@yield('content')
@yield('script')
</body>
</html>