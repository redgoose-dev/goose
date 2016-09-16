## Introduce
이 모듈은 회원관리 모듈입니다.  
회원의 정보가 출력되는 정보목록을 확인할 수 있으며, 회원추가, 수정, 삭제할 수 있습니다.  
회원정보에 권한이라는 항목이 있습니다. 이것은 Goose에서 다른 모듈이나 컨텐츠에 접근할 수 있는 권한의 수치입니다. __각 모듈에서 원하는 권한값보다 값이 높으면 접근할 수 있거나 모듈을 관리할 수 있습니다.__

Goose 관리자 접근도 권한 레벨이 설정값보다 높으면 로그인가능합니다.  
관리자 접근권한 설정은 `{goose}/data/config.php` 파일의 `$accessLevel` 배열변수에서 할 수 있습니다.
* login : 접근 권한을 가지고 있습니다.
* admin : 모든 권한을 가지는 관리자를 가지고 있습니다.



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



## Database field

모듈을 설치할때 사용되는 db 필드들입니다.

| Field         | Type       | Comment
| : ----------: | :--------: | :----------------------------
| srl           | int        | 고유번호
| email         | varchar    | email
| name          | varchar    | 이름
| pw            | varchar    | 비밀번호
| level         | int        | 권한값
| regdate       | varchar    | 등록날짜



## Module API

모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.

```
$user = new\User\User();
$user = core\Module::load('User');
```

#### $mod->transaction()
회원을 등록하거나 수정, 삭제 처리합니다.

```
$result_make = $user->transaction('create', $_POST); // make
$result_modify = $user->transaction('modify', $_POST); // modify
$result_remove = $user->transaction('remove', $_POST); // remove
```

`$_POST`값에 대해서는 `/mod/User/skin/default/form.blade.php` 파일을 참고해주세요.