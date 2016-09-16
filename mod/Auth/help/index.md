## Introduce
인증 폼이 출력되고, 인증 처리를 하는 모듈입니다.


## URL guide

#### 로그인 인증 폼
`{goose}/Auth/login/`

#### 로그아웃 처리
`{goose}/auth/logout/`



## setting.json
모듈의 환경설정 파일입니다. 설정에 대한 소개는 다음과 같습니다.

#### name
모듈의 id값

#### title
출력되는 제목값

#### description
모듈의 설명

#### skin
다른형태로 목록이나 폼 페이지가 출력되는 스킨값



## 로그인폼에서 로그인 처리하기
로그인은 전송폼으로 이메일와 비밀번호 값을 POST형식으로 전송하여 처리합니다. 다음은 전송폼 필드의 name값입니다.

```
<form action="/mod/Auth/login/">
    <input type="email" name="email" />
    <input type="password" name="password" />
    <button type="submit">submit</button>
</form>
```

* `/mod/Auth/login/` : form 엘리먼트에서 action 속성값으로 넣는 주소
* `[name=email]` : 이메일 주소
* `[name=password]` : 비밀번호