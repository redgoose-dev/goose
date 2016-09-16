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
				<dd><textarea name="content" id="content" rows="15" class="block">{{ htmlspecialchars($repo->article['content']) }}</textarea></dd>
			</dl>

			<dl class="gs-webz">
				<dt><label for="upload">파일 업로드</label></dt>
				<dd>
					<input type="file" name="upload[]" id="upload" multiple/>
				</dd>
			</dl>

			@if(count($repo->file))
			<dl class="gs-webz">
				<dt><label for="upload">첨부 파일목록</label></dt>
				<dd>
					<p>체크한 이미지는 삭제됩니다.</p>
					<article class="attach-files">
						<ul>
							@foreach($repo->file as $file)
							<li>
								<label>
									<input type="checkbox" name="removeFiles[]" value="{{ $file['srl'] }}"/>
									<span>{{ $file['name'] }} {{ core\Util::getFileSize($file['size']) }}</span>
								</label>
								<a href="{{ $root }}/{{ $file['loc'] }}" target="_blank" class="gs-brk-type">open file</a>
							</li>
							@endforeach
						</ul>
					</article>
				</dd>
			</dl>
			@endif
		</fieldset>

		<nav class="gs-btn-group right">
			<button type="submit" class="gs-button col-key">{{ $typeName }}</button>
			<button type="button" class="gs-button" onclick="history.back(-1)">뒤로가기</button>
		</nav>
	</form>
</section>
@endsection