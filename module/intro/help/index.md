### Introduce
관리자 인증 후 첫번째로 표시되는 화면의 모듈입니다. 직접 모듈에 들어가지 않고도 일부 컨텐츠를 우선적으로 볼 수 있게 할 수 있습니다.  
만약 다른형태의 첫화면을 만들고 싶다면 default 스킨을 참고하여 새로운 스킨을 만드세요.


### Address guide
* __`{goose}/`__  
실행되는 intro 모듈


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