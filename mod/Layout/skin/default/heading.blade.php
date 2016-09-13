<header class="gs-heading">
	@if ($title)
		<h1>
			@if($titleType)
			<em class="gs-brk-type">{{ $titleType }}</em>
			@endif
			<span>{{ $title }}</span>
		</h1>
	@endif
	@if ($description)
		<p>{!! $description !!}</p>
	@endif
	@if ($mod->isAdmin && $isHeadNavigation)
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