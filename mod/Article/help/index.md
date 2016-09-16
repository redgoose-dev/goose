## Introduce

Goose 프로그램에서 기본적으로 제공하는 모듈중에 가장 많이 사용되는 글 관리 모듈입니다.



## URL guide

#### 모든 Article의 목록
`/mod/Article/index/`

#### 둥지를 선택한 article의 목록
`/mod/Article/index/{nest_srl}/`

#### 글 본문 페이지 주소
`/mod/Article/read/{srl}/`

#### 글 작성
`/mod/Article/create/{nest_srl}/`

#### 글 수정
`/mod/Article/modify/{srl}/`

#### 글 삭제
`/mod/Article/remove/{srl}/`



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

#### enableUpdateHit
조회수 업데이트를 할것인지에 대한 여부



## Database field

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



## Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.

```
$article = new mod\Article\Article();
$article = core\Module::load('Article');
```

#### $mod->transaction()
글을 등록하거나 수정, 삭제 처리합니다.

```
$result_make = $article->transaction('create', $_POST); // make
$result_modify = $article->transaction('modify', $_POST); // modify
$result_remove = $article->transaction('remove', $_POST); // remove
```

`$_POST`값에 대해서는 `/mod/Article/skin/default/form.blade.php` 파일을 참고해주세요.

#### $mod->updateHit()
해당글의 조회수를 조절합니다.

```
$result = $mod->updateHit(12, 1); // (article_srl, 더하는 숫자)
```