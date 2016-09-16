## Introduce
관리자에서 일반페이지를 출력하는 모듈입니다.


## Address guide

#### 페이지 목록
`/mod/Page/`  

#### `{page}.html` 페이지
`/mod/Page/{page}/`



## setting.json
모듈의 환경설정 파일입니다. 설정에 대한 소개는 다음과 같습니다.

#### name
모듈의 id값

#### title
출력되는 제목값

#### description
모듈의 설명

### permission
접근권한 번호



## Add page
`/mod/Page/pages/`경로에서 `xyz.html`파일을 추가하고 `http://{goose}/Page/xyz/`경로로 접속하면 추가한 파일로 접속할 수 있습니다.  
pages 폴더속에 만든 html파일들은 `http://{goose}/Page/`로 접속하면 목록으로 확인 가능하고 쉽게 열 수 있습니다.