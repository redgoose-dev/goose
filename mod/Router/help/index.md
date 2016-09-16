## Introduce
이 모듈은 url 주소를 처리해주는 모듈입니다.

`http://fruits.com/apple/banana/12/`형태의 url 주소를 어떻게 이동하는지에 대하여 결정하고, 값을 전달받을 수 있게 도와주는 역할을 합니다.  
[AltoRouter](https://github.com/dannyvankooten/AltoRouter) 클래스를 사용하며 모듈을 로드할때 같이 AltoRouter 클래스 인스턴스 변수로 만듭니다.



## setting.json
모듈의 환경설정 파일입니다. 설정에 대한 소개는 다음과 같습니다.

#### name
모듈의 id값

#### title
출력되는 제목값

#### description
모듈의 설명

#### permission
접근권한 번호

#### basicModule
`/`로 접근했을때 실행되는 모듈이름



## AltoRouter
router 모듈에서 사용되는 php 클래스를 설치하고 사용법을 알려주는 url은 다음과 같습니다.

http://altorouter.com/usage/install.html


## 앱 제작할때의 사용법
앱을 만들때 이 모듈을 불러와서 활용할 수 있습니다.  
router 모듈을 활용한 소스는 다음과 같이 `모듈 불러오기 -> 루트 경로 설정 -> 라우트 맵 설정 -> match 설정 -> url에 맞춰 실행` 순서로 실행됩니다.

```
// load router module
$router = core\Module::load('Router');
// set base path
$router->route->setBasePath('/');

// set route map
$router->route->map('GET', '/', 'index');
$router->route->map('GET', '/page/[a:page]', 'page');
$router->route->map('GET', '/nest/[a:nest]', 'nest');

// set match
$router->match = $router->route->match();

// action route
if ($router->match)
{
	$_target = $router->match['target'];
	$_params = $router->match['params'];
	$_method = $_SERVER['REQUEST_METHOD'];

	switch($_target)
	{
		case 'page':
			// $_target : page, $_params['page']
			break;
		case 'nest':
			// $_target : nest, $_params['nest']
			break;
	}
}
```