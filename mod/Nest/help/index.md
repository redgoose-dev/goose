## Introduce
Nest(둥지)라는 모듈은 Article모듈에 있는 글들의 그룹이라고 볼 수 있습니다. 다시말해 article의 글들을 담아놓은 바구니라고 볼 수 있습니다.  
단순히 article의 그룹화 시키는것뿐만 아니라 둥지의 성격이나 특징을 특화하여 개성있는 둥지를 만들어 관리할 수 있습니다. Goose 프로그램의 핵심적인 모듈이라고 할 수 있습니다.

![nest index page](./assets/page-001.png)
처음 접속하는거라면 아무것도 없을겁니다.



## Address guide

#### 둥지목록
`/mod/Nest/index/`

#### app_srl을 선택한 둥지의 목록
`/mod/Nest/index/{app_srl}/`

#### 둥지만들기
`/mod/Nest/create/`

#### 둥지수정
`/mod/Nest/modify/{srl}/`

#### 둥지삭제
`/mod/Nest/remove/{srl}/`



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

#### countArticle
한페이지에 출력되는 `article`갯수

#### articleListTypes
article 목록타입

#### thumbnailSize
썸네일 이미지 사이즈 설정



## Database field
다음은 nest 모듈을 설치할때 사용되는 db 필드들입니다.

| Field      | Type       | Comment
| : -------: | :--------: | :----------------------------
| srl        | int        | 고유번호
| app_srl    | int        | app 모듈의 고유번호
| id         | varchar    | 고유 id값
| name       | varchar    | 이름
| json       | text       | 유동적인 json타입의 설정값
| regdate    | varchar    | 날짜



## Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.
```
$nest = new mod\Nest\Nest();
$nest = Module::load('Nest');
```

#### $nest->transaction()
글을 등록하거나 수정, 삭제 처리합니다.
```
$result_make = $nest->transaction('create', $_POST); // make
$result_modify = $nest->transaction('modify', $_POST); // modify
$result_remove = $nest->transaction('remove', $_POST); // remove
```  
`$_POST`값에 대해서는 `/mod/Nest/skin/default/form.blade.php` 파일을 참고해주세요.