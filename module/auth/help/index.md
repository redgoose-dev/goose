### Introduce
인증 폼이 출력되고, 인증 처리를 하는 모듈입니다.


### Address guide
* `{goose}/auth/login/`  
로그인 인증 폼 페이지

* `{goose}/auth/logout/`  
이 주소로 접속하면 로그아웃 처리됩니다.


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