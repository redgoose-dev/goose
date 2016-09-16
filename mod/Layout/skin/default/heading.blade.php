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
	@if ($isHeadNavigation)
		<nav>
			@if($isHeadNavigation['help'])
			<a href="{{ $root }}/Help/{{ $modName or $mod->name }}/" class="help" title="go to help">
				<i class="material-icons">help</i>
			</a>
			@endif
			@if($isHeadNavigation['setting'] && $mod->isAdmin)
			<a href="{{ $root }}/Modules/editSetting/{{ $modName or $mod->name }}/" class="setting" title="edit setting">
				<i class="material-icons">settings</i>
			</a>
			@endif
		</nav>
	@endif
</header>