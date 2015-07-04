### Introduce
인증 폼이 출력되고, 인증 처리를 하는 모듈입니다.


### Address guide
* `{goose}/auth/login/`  
로그인 인증 폼 페이지

* `{goose}/auth/logout/`  
이 주소로 접속하면 로그아웃 처리됩니다.


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

* __skin__  
다른형태로 목록이나 폼 페이지가 출력되는 스킨값


### 로그인폼에서 로그인 처리하기
로그인은 전송폼으로 이메일와 비밀번호 값을 POST형식으로 전송하여 처리합니다. 다음은 전송폼 필드의 name값입니다.

```
<form action="{goose}/auth/login/">
    <input type="email" name="email" />
    <input type="password" name="password" />
    <button type="submit">submit</button>
</form>
```

* __{goose}/auth/login/__ : form 엘리먼트에서 action 속성값으로 넣는 주소
* __[name=email]__ : 이메일 주소
* __[name=password]__ : 비밀번호