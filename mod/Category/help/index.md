## Introduce
이 모듈은 Article 모듈의 글의 분류로 묶기위해 사용되며 nest 모듈에서 사용유무를 정할 수 있습니다.



## URL guide

#### 둥지를 선택한 분류의 목록
`/mod/Category/index/{nest_srl}/`

#### 분류 만들기
`/mod/Category/create/{nest_srl}/`

#### 분류 수정
`/mod/Category/modify/{nest_srl}/{srl}/`

#### 분류 삭제
`/mod/Category/remove/{nest_srl}/{srl}/`



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

모듈을 설치할때 사용되는 db 필드들입니다.

| Field      | Type       | Comment
| : -------: | :--------: | :----------------------------
| srl        | int        | 고유번호
| nest_srl   | int        | nest 모듈의 srl번호
| turn       | int        | 츌력순서
| name       | varchar    | 이름
| regdate    | varchar    | 날짜



## Module API

모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.

```
$category = new mod\Category\Category();
$category = core\Module::load('Category');
```

#### $mod->transaction()
글을 등록하거나 수정, 삭제 처리합니다.
```
$result_make = $category->transaction('create', $_POST); // make
$result_modify = $category->transaction('modify', $_POST); // modify
$result_remove = $category->transaction('remove', $_POST); // remove
$result_sort = $category->transaction('sort', $_POST); // sort
```
`$_POST`값에 대해서는 `/mod/Category/skin/default/form.blade.php` 파일을 참고해주세요.