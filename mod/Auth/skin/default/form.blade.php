@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => 'Admin authentication',
		'description' => $head['description']
	])

	<form action="{{ $root }}/auth/login/" method="post" class="form-auth">
		<input type="hidden" name="redir" value="{{ $_SERVER['REQUEST_URI'] }}">
		<fieldset>
			<legend class="blind">authentication form</legend>
			<label>
				<strong>Email</strong>
				<input type="email" name="email" maxlength="40" placeholder="email address" class="block" value="{{ $user['email'] }}" />
			</label>
			<label>
				<strong>Password</strong>
				<input type="password" name="password" maxlength="20" placeholder="password" class="block" value="{{ $user['password'] }}" />
			</label>
		</fieldset>
		<nav class="gs-btn-group">
			<button type="submit" class="gs-button block col-key">Login</button>
		</nav>
	</form>
</section>
@endsection