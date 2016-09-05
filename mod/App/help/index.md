### Introduce
App 모듈은 Nest를 그룹으로 묶기위해 사용되는 모듈입니다.  
[About the Goose](http://redgoosedev.github.io/goose/#Introduce/AboutTheGoose) 페이지에서 설명한대로 여러가지 스타일의 사이트나 앱을 만들때 nest나 article들을 묶어서 관리하기 위하여 만들었습니다.  
프로젝트 이름으로 사용하는것을 권장합니다.


### Address guide
* `{goose}/app/`, `{goose}/app/index/`  
모든 앱 목록

* `{goose}/app/create/`  
앱 만들기 페이지

* `{goose}/app/modify/{srl}/`  
앱 수정 페이지

* `{goose}/app/remove/{srl}/`  
앱 삭제 페이지


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

* __allowApi__  
API 모듈을 통하여 데이터를 열람할 수 있는 권한설정값입니다. (기능확정되지 않았습니다.)


### Database field
App 모듈을 설치할때 사용되는 db 필드들입니다.

| Field      | Type       | Comment
| : -------: | :--------: | :----------------------------
| srl        | int        | 고유번호
| id         | varchar    | 고유 id값
| name       | varchar    | 이름
| regdate    | varchar    | 날짜


### Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.
```
$mod = Module::load('app');
```

##### $mod->getCount()
조건에 맞는 글 갯수를 가져옵니다.  
```
$count = $mod->getCount();
```

##### $mod->getItems()
조건에 맞는 글들을 모음을 가져옵니다.
```
$data = $mod->getItems();
```

##### $mod->getItem()
조건에 맞는 글 한개만 가져옵니다.
```
$data = $mod->getItem(array(
	'where' => 'srl=1'
));
```

##### $mod->transaction()
글을 등록하거나 수정, 삭제 처리합니다.
```
$result_make = $mod->transaction('create', $_POST); // make
$result_modify = $mod->transaction('modify', $_POST); // modify
$result_remove = $mod->transaction('remove', $_POST); // remove
```
$\_POST값에 대해서는 `{module}/skin/default/view_form.php` 파일을 참고해주세요.