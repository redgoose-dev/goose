### Introduce
관리자에서 일반페이지를 출력하는 모듈입니다.


### Address guide
* `{goose}/page/`  
등록된 페이지 목록을 확인할 수 있는 페이지로 이동합니다.

* `{goose}/page/[page]/`  
\[page\]페이지를 불러옵니다.


### setting.json
모듈의 환경설정 파일입니다. 설정에 대한 소개는 다음과 같습니다.

* __name__  
모듈의 id값

* __title__  
출력되는 제목값

* __description__  
모듈의 설명

* __permission__  
접근권한 번호


### 페이지 추가
`{goose}/modules/page/pages/`경로에서 xyz.html파일을 추가하고 `http://{goose}/page/xyz/`경로로 접속하면 추가한 파일로 접속할 수 있습니다.  
pages 폴더속에 만든 html파일들은 `http://{goose}/page/`로 접속하면 목록으로 확인 가능하고 쉽게 열 수 있습니다.