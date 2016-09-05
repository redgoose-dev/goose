<header class="gs-heading">
	@if ($title)
		<h1>{{ $title }}</h1>
	@endif
	@if ($description)
		<p>{{ $description }}</p>
	@endif
	@if ($mod->isAdmin)
		<nav>
			<a href="{{ $root }}/Help/{{ $mod->name }}/" class="help" title="go to help">
				<i class="material-icons">help</i>
			</a>
			<a href="{{ $root }}/Modules/editSetting/{{ $mod->name }}/" class="setting" title="edit setting">
				<i class="material-icons">settings</i>
			</a>
		</nav>
	@endif
</header>