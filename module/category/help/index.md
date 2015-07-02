### Introduce
이 모듈은 Article 모듈의 글의 분류로 묶기위해 사용되며 nest 모듈에서 사용유무를 정할 수 있습니다.


### Address guide
* `{goose}/category/index/{nest_srl}/`  
둥지를 선택한 분류의 목록

* `{goose}/category/create/{nest_srl}/`  
분류 만들기 페이지

* `{goose}/category/modify/{nest_srl}/{srl}/`  
분류 수정 페이지

* `{goose}/category/remove/{nest_srl}/{srl}/`  
분류 삭제 페이지


### setting.json
모듈의 환경설정 파일입니다. 설정에 대한 소개는 다음과 같습니다.

* __name__  
모듈의 id값

* __title__  
출력되는 제목값

* __description__  
모듈의 설명

* __permission__  
접근권한 번호 (숫자가 높을수록 권한이 높습니다.)

* __adminPermission__  
모듈 관리자 권한 번호 (숫자가 높을수록 권한이 높습니다.)

* __install__  
인스톨이 필요한 모듈인지에 대한 유무를 정합니다.

* __skin__  
다른형태로 목록이나 폼 페이지가 출력되는 스킨값

* __pagePerCount__  
한페이지에 출력되는 글 갯수


### Database field

모듈을 설치할때 사용되는 db 필드들입니다.

| Field      | Type       | Comment
| : -------: | :--------: | :----------------------------
| srl        | int        | 고유번호
| nest_srl   | int        | nest 모듈의 srl번호
| turn       | int        | 츌력순서
| name       | varchar    | 이름
| regdate    | varchar    | 날짜


### Module API

모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.
```
$mod = Module::load('category');
```

* __$mod->getCount()__  
조건에 맞는 글 갯수를 가져옵니다.  
```
$count = $mod->getCount();
```

* __$mod->getItems()__  
조건에 맞는 글들을 모음을 가져옵니다.
```
$data = $mod->getItems();
```

* __$mod->getItem()__  
조건에 맞는 글 한개만 가져옵니다.
```
$data = $mod->getItem(array(
	'where' => 'srl=1'
));
```

* __$mod->transaction()__  
글을 등록하거나 수정, 삭제 처리합니다.
```
$result_make = $mod->transaction('create', $_POST); // make
$result_modify = $mod->transaction('modify', $_POST); // modify
$result_remove = $mod->transaction('remove', $_POST); // remove
$result_sort = $mod->transaction('sort', $_POST); // sort
```
$\_POST값에 대해서는 `{module}/skin/default/view_form.php` 파일을 참고해주세요.