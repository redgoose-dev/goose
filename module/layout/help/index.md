### 소개
이 관리자의 레이아웃을 담당하는 모듈입니다.  
관리자 화면을 출력하는 대부분의 모듈이 이 모듈을 사용하면서 화면 껍데기를 출력합니다. 이 모듈의 스킨을 바꾸는것으로 관리자의 디자인이 크게 변화됩니다.


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

* __skin__  
이 모듈의 스킨입니다.


### 활용
개발한 모듈에서 레이아웃을 씌워서 출력할때 다음의 형식으로 사용할 수 있습니다.

```
// create layout module instance value
$this->layout = Module::load('layout');

// set page pwd
$this->pwd_container = '{goose}/module/{moduleName}/skin/{skinName}/{pageName}.html';

// require layout
require_once($this->layout->getUrl());
```