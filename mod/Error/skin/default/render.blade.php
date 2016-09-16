@extends($mod->skinAddr.'.layout')


@section('content')
<main class="full-screen">
	<section class="wrap">
		<h1 title="Goose">Error</h1>
		<p class="code">{{ $code }}</p>
		<p class="message">{{ $message }}</p>
		<nav>
			<a href="{{ $homeUrl }}" title="home">
				<span class="ball"><i class="material-icons">home</i></span>
				<span class="text">Go to home</span>
			</a>
		</nav>
	</section>
</main>
@endsection