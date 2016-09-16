## Introduce
Goose 레이아웃을 담당하는 모듈입니다.  
관리자 화면을 출력하는 대부분의 모듈이 이 모듈을 사용하면서 화면 껍데기를 출력합니다. 이 모듈의 스킨을 바꾸는것으로 관리자의 디자인이 크게 변화됩니다.


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

#### skin
이 모듈의 스킨입니다.


## How to use
레이아웃은 `core\Blade` 클래스의 `render()`메서드에서 사용되고 있습니다. 활용하는 방법은 다음과 같습니다.

```
$blade = new core\Blade();
$blade->render('Nest.skin.default.index', [
	'foo' => 'bar'
]);
```

`Nest.skin.default.index` 키워드는 `/mod/Nest/skin/default/index.blade.php` 경로를 의미합니다.