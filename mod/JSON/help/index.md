## Introduce
JSON 데이터를 관리하는 모듈입니다.  
json데이터를 데이터베이스에 저장하여 추가, 수정, 삭제 할 수 있으며 이 데이터를 다양한 곳에 활용할 수 있는 멋진 모듈입니다.



## Address guide

#### 모든 json 데이터의 목록
`/mod/JSON/index/`

#### json 데이터가 표시되는 상세내용
`/mod/JSON/read/{srl}/`

#### json 만들기
`/mod/JSON/create/`

#### json 수정
`/mod/JSON/modify/{srl}/`

#### json 삭제
`/mod/JSON/remove/{srl}/`



## setting.json
모듈의 환경설정 파일입니다. 설정에 대한 소개는 다음과 같습니다.

#### name
모듈의 id값

#### title
출력되는 제목값

#### description
모듈의 설명

#### permission
접근권한 번호 (숫자가 높을수록 권한이 높습니다.)

#### adminPermission
모듈 관리자 권한 번호 (숫자가 높을수록 권한이 높습니다.)

#### install
인스톨이 필요한 모듈인지에 대한 유무를 정합니다.

#### skin
다른형태로 목록이나 폼 페이지가 출력되는 스킨값

#### pagePerCount
한페이지에 출력되는 글 갯수



## Database field
App 모듈을 설치할때 사용되는 db 필드들입니다.

| Field      | Type         | Comment
| : -------: | :----------: | :----------------------------
| srl        | int          | 고유번호
| name       | varchar      | 고유 id값
| json       | mediumtext   | json 데이터
| regdate    | varchar      | 날짜



## Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.
```
$json = new mod\JSON\JSON();
$json = core\Module::load('JSON');
```

#### $mod->transaction()__
글을 등록하거나 수정, 삭제 처리합니다.
```
$result_make = $json->transaction('create', $_POST); // make
$result_modify = $json->transaction('modify', $_POST); // modify
$result_remove = $json->transaction('remove', $_POST); // remove
```
`$_POST`값에 대해서는 `/mod/JSON/skin/default/form.blade.php` 파일을 참고해주세요.