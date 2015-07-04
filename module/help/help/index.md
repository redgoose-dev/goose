### Introduce
모듈의 도움말의 표시를 담당하는 모듈입니다.


### Address guide
* __`{goose}/help/`__  
도움말 페이지를 볼 수 있는 모듈의 목록

* __`{goose}/help/{moduleName}`__  
{moduleName}의 도움말 페이지. index.md 나 index.html 페이지가 자동으로 실행됩니다.

* __`{goose}/help/{moduleName}/{filename}/`__  
도움말의 특정 페이지 `{filename}.html` 파일이나 `{filename}.md` 파일이 열립니다.


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


### markdown(.md) 파일에서 image 파일을 불러서 표시하는 방법
markdown에서 이미지를 표시하는 코드는 `![alt](image.png)` 이런 형식입니다.  
문서가 저장되어있는 위치에 example.png 이미지가 들어있을때 표시하는 방법은 `![alt](./image.png)` 형식으로 지정해주면 됩니다.  
문서가 저장되어있는 곳에서 img 라는 폴더가 있고 그 속에 이미지가 들어있으면 `![alt](./img/image.png)` 형식으로 이미지를 불러오면 됩니다.