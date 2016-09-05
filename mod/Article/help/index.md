### Introduce
Goose 프로그램에서 기본적으로 제공하는 모듈중에 가장 많이 사용되는 글 관리 모듈입니다.


### Address guide
* `{goose}/article/index/`  
모든 article의 목록. 다음 항목과 같이 둥지를 통하여 접근하는것을 권장합니다.
* `{goose}/article/index/{nest_srl}/`  
둥지를 선택한 article의 목록
* `{goose}/article/read/{srl}/`  
글 본문 페이지 주소입니다.
* `{goose}/article/create/{nest_srl}/`  
글 작성 페이지
* `{goose}/article/modify/{srl}/`  
글 수정 페이지
* `{goose}/article/remove/{srl}/`  
글 삭제 페이지


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

| Field         | Type       | Comment
| : ----------: | :--------: | :----------------------------
| srl           | bigint     | 고유번호
| app_srl       | int        | app 모듈의 srl번호
| nest_srl      | int        | nest 모듈의 srl번호
| category_srl  | int        | category 모듈의 srl번호
| user_srl      | int        | user 모듈의 srl번호
| title         | varchar    | 제목
| content       | longtext   | 내용
| hit           | int        | 조회수
| json          | text       | json 데이터
| ip            | varchar    | 작성자 ip주소
| regdate       | varchar    | 등록날짜
| modate        | varchar    | 수정날짜


### Module API

모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.
```
$mod = Module::load('article');
```

#### $mod->getCount()
조건에 맞는 글 갯수를 가져옵니다.  
```
$count = $mod->getCount();
```

#### $mod->getItems()
조건에 맞는 글들을 모음을 가져옵니다.
```
$data = $mod->getItems();
```

#### $mod->getItem()
조건에 맞는 글 한개만 가져옵니다.
```
$data = $mod->getItem(array(
	'where' => 'srl=1'
));
```

#### $mod->transaction()
글을 등록하거나 수정, 삭제 처리합니다.
```
$result_make = $mod->transaction('create', $_POST); // make
$result_modify = $mod->transaction('modify', $_POST); // modify
$result_remove = $mod->transaction('remove', $_POST); // remove
```
$_POST값에 대해서는 `{module}/skin/default/view_form.php` 파일을 참고해주세요.

#### $mod->updateHit()
해당글의 조회수를 조절합니다.
```
$result = $mod->updateHit(1234, 1); // (article_srl, 조절하는 숫자)
```
