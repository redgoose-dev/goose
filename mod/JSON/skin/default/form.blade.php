@extends($layout->skinAddr.'.layout')


@section('content')
<section>
	@include($layout->skinAddr.'.heading', [
		'title' => $mod->set['title'] . ' ' . $typeName,
		'description' => $mod->set['description'],
		'isHeadNavigation' => true
	])

	<form action="{{ $root }}/{{ $mod->name }}/{{ $action }}/" method="post" name="post" id="regsterForm">
		<input type="hidden" name="json_srl" value="{{ $repo->json['srl'] }}">
		<input type="hidden" name="json" value="{{ $repo->json['json'] }}">

		<fieldset class="form-group">
			<dl class="gs-webz">
				<dt><label for="name">이름</label></dt>
				<dd>
					<input
						type="text" name="name" id="name"
						size="22" maxlength="50" class="block"
						value="{{ $repo->json['name'] }}">
				</dd>
			</dl>
			<dl>
				<dt><label>JSON DATA</label></dt>
				<dd>
					<div class="get-source" id="getSource">
						<ul>
							<li>
								<button type="button" class="gs-button size-small" data-action="toggle_form">get source</button>
							</li>
						</ul>
						<article class="form">
							<div class="wrap">
								<h1>Get JSON source</h1>
								<p>가져올 json타입의 데이터를 입력해주세요. 아래 버튼중에 <b>[import]</b>는 추가, <b>[replace]</b>는 새로 내용을 넣습니다.</p>
								<textarea name="import_json">{}</textarea>
								<nav class="gs-btn-group center">
									<button type="button" class="gs-button" data-action="getSourceClose">Close</button>
									<button type="button" class="gs-button col-key" data-action="getSourceImport">Import</button>
									<button type="button" class="gs-button col-key" data-action="getSourceReplace">Replace</button>
								</nav>
							</div>
						</article>
					</div>
					<hr class="clear">
					<div class="JSONEditor" id="JSONEditor"></div>
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
<link rel="stylesheet" href="{{ $root }}/vendor/JSONEditor/dist/css/JSONEditor.css" media="screen" />
<link rel="stylesheet" href="{{ $root }}/{{ $mod->skinPath }}css/json.css">
@endsection

@section('script')
<script src="{{ $root }}/vendor/JSONEditor/dist/js/JSONEditor.class.js"></script>
<script src="{{ $root }}/vendor/JSONEditor/vendor/jquery-sortable/jquery-sortable.min.js"></script>
<script src="{{ $root }}/vendor/validation/jquery.validate.min.js"></script>
<script src="{{ $root }}/vendor/validation/localization/messages_ko.js"></script>
<script src="{{ $root }}/{{ $mod->skinPath }}js/json.js"></script>
@endsection