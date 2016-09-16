## Introduce
모듈의 도움말의 표시를 담당하는 모듈입니다.



## Address guide

#### 도움말 페이지를 볼 수 있는 모듈의 목록
`/mod/Help/`

#### {moduleName}의 도움말 페이지
`/mod/Help/{moduleName}/`

#### 도움말 {filename} 페이지
`/mod/Help/{moduleName}/{filename}/`



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

#### skin
다른형태로 목록이나 폼 페이지가 출력되는 스킨값



## markdown(.md) 파일에서 image 파일을 불러서 표시하는 방법
markdown에서 이미지를 표시하는 코드는 `![alt](image.png)` 이런 형식입니다.  
문서가 저장되어있는 위치에 example.png 이미지가 들어있을때 표시하는 방법은 `![alt](./image.png)` 형식으로 지정해주면 됩니다.  
문서가 저장되어있는 곳에서 img 라는 폴더가 있고 그 속에 이미지가 들어있으면 `![alt](./img/image.png)` 형식으로 이미지를 불러오면 됩니다.