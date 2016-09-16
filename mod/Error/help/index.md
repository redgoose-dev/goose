## Introduce
오류를 처리하는 모듈입니다.  
에러 상황이 일어났을때 페이지로 이동하거나 메세지 박스를 출력할 수 있습니다.



## Module API
모듈에서 제공하는 api입니다. 우선 다음과 같이 모듈 인스턴스 변수값에 담아야합니다.
```
$error = new mod\Error\Error();
$error = core\Module::load('Error');
```

#### 전체화면으로 에러메세지 출력
전체 화면으로 에러메세지를 표시합니다.  
```
$error->render(123, 'critical error');
```

#### 화면 일부분으로 에러메세지 출력
화면 일부분에 에러메세지를 표시합니다.
```
$error->box(123, 'box error');
```