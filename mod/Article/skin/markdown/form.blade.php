@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'titleType' => $repo->nest['name'],
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => [ 'help' => true, 'setting' => true ]
	])

	<form name="writeForm" action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post" enctype="multipart/form-data">
		<input type="hidden" name="app_srl" value="{{ $repo->nest['app_srl'] }}">
		<input type="hidden" name="nest_srl" value="{{ $repo->nest['srl'] }}">
		<input type="hidden" name="article_srl" value="{{ $article_srl }}">
		<input type="hidden" name="skin" value="{{ $repo->nest['json']['articleSkin'] }}">
		<input type="hidden" name="page" value="{{ $_GET['page'] }}">
		<input type="hidden" name="json" value="" />
		<input type="hidden" name="m" value="{{ $_GET['m'] }}">
		<input type="hidden" name="addQueue" />
		<input type="hidden" name="thumbnail_image" />

		<fieldset class="form-group">
			@if(count($repo->category))
			<dl class="gs-webz">
				<dt><label for="category">분류</label></dt>
				<dd>
					<select name="category_srl" id="category">
						<option value="">분류선택</option>
						@foreach($repo->category as $category)
							<?php $selected = ($repo->article['category_srl'] == $category['srl'] || $category_srl==$category['srl']) ? ' selected' : '' ?>
							<option value="{{ $category['srl'] }}"{{ $selected }}>{{ $category['name'] }}</option>
						@endforeach
					</select>
				</dd>
			</dl>
			@endif

			<dl class="gs-webz">
				<dt><label for="title">제목</label></dt>
				<dd><input type="text" id="title" name="title" class="block" value="{{ htmlspecialchars($repo->article['title']) }}" /></dd>
			</dl>

			<dl>
				<dt><label for="content">내용</label></dt>
				<dd>
					<div class="mk-editor">
						<nav>
							<a href="#" data-control="edit" class="active">
								<i class="material-icons">code</i>
								<span>Edit</span>
							</a>
							<a href="#" data-control="preview">
								<i class="material-icons">remove_red_eye</i>
								<span>Preview</span>
							</a>
						</nav>
						<div class="body">
							<div class="show" data-target="edit">
								<textarea name="content" id="content" rows="20" class="block">{{ htmlspecialchars($repo->article['content']) }}</textarea>
								<p>
									* 존 그루버 마크다운 페이지 번역 : <a href="http://nolboo.github.io/blog/2013/09/07/john-gruber-markdown/" target="_blank">link</a><br/>
									* Markdown Extra : <a href="https://michelf.ca/projects/php-markdown/extra/" target="_blank">link</a>
								</p>
							</div>
							<div data-target="preview"></div>
						</div>
					</div>
				</dd>
			</dl>

			<dl>
				<dt><label>파일첨부</label></dt>
				<dd>
					<article class="rg-uploader" id="queuesManager">
						<div class="body" data-comp="queue">
							<div class="col queue is-large-size" data-element="queue"><ul></ul></div>
						</div>
						<footer>
							<nav>
								<label class="add-file">
									<input type="file" data-element="addfiles" multiple>
									<i class="material-icons">add_circle_outline</i>
									<span>Add files</span>
								</label>
							</nav>
							<div class="size-info"></div>
						</footer>
					</article>
				</dd>
			</dl>
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
		</nav>
	</form>
</section>
@endsection

@section('style')
<link rel="stylesheet" href="{{ $root }}/{{ $mod->skinPath }}css/markdown.css">
<link rel="stylesheet" href="{{ $root }}/vendor/rg-Uploader/dist/css/rg-uploader.min.css">
<link rel="stylesheet" href="{{ $root }}/vendor/Parsedown/markdown.css">
@endsection

@section('script')
<script src="{{ $root }}/vendor/rg-Uploader/dist/js/rg-uploader.pkgd.js"></script>
<script src="{{ $root }}/vendor/rg-Uploader/dist/js/plugin.pkgd.js"></script>
<script>
// set user data
var userData = {
	form : document.writeForm,
	root : '{{ $root }}',
	url : '{{ $url }}/',
	originalPath : '{{ $file->set['upPath_original'] }}/',
	previewScriptPath : '{{ $root }}/Script/run/markdown_preview/',
	pushDatas : function(src) {
		try {
			return JSON.parse(decodeURIComponent(src));
		} catch(e) {
			return [];
		}
	}('{!! require_once($pwd.$mod->skinPath.'lib/attachFiles.php') !!}'),
	articleData : function(src) {
		try {
			return JSON.parse(decodeURIComponent(src));
		} catch(e) {
			return {};
		}
	}('{{ (isset($repo->article['json'])) ? core\Util::arrayToJson($repo->article['json'], true) : '' }}'),
	thumbnailSet : {
		type : '{{ $repo->nest['json']['thumbnail']['type'] }}',
		size : {
			width : parseInt('{{ $repo->nest['json']['thumbnail']['size'][0] }}' || 150),
			height : parseInt('{{ $repo->nest['json']['thumbnail']['size'][1] }}' || 150)
		}
	},
	thumbnail : {},
	thumbnail_image : '',
	addQueue : []
};
</script>
<script src="{{ $root }}/{{ $mod->skinPath }}js/markdown.js"></script>
@endsection