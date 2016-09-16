<?php
namespace mod\Auth;
use core;
if (!defined('__GOOSE__')) exit();


class Auth {

	public $name, $set, $path, $params;
	public $skinPath, $skinAddr, $message;

	public function __construct($params=[])
	{
		core\Module::initModule($this, $params);
	}


	/**
	 * index method
	 */
	public function index()
	{
		switch($this->params['action'])
		{
			case "login":
				if ($this->params['method'] == 'POST')
				{
					$this->login($_POST['email'], $_POST['password'], $_POST['redir']);
				}
				else
				{
					self::loginForm();
				}
				break;

			case "logout":
				self::logout();
				break;

			default:
				echo "Authentication page";
				break;
		}
	}

	/**
	 * auth
	 *
	 * @param int $level goose 접근할 수 있는 레벨값
	 */
	public function auth($level=0)
	{
		if ($this->params['action'] == 'logout')
		{
			self::index();
		}
		else if ($level && ($level > $_SESSION['goose_level']))
		{
			$this->params['action'] = 'login';
			if ($level && $_SESSION['goose_level'])
			{
				$this->message['headDescription'] = '접근 권한이 없습니다.';
			}
			self::index();
			core\Goose::end();
		}
	}

	/**
	 * login form
	 */
	public function loginForm()
	{
		// set blade class
		$blade = new core\Blade();

		// create layout module
		$layout = core\Module::load('Layout');

		// set user
		$user = [
			'email' => (isset($_GET['userEmail'])) ? $_GET['userEmail'] : '',
			'password' => (isset($_GET['userPassword'])) ? $_GET['userPassword'] : ''
		];

		// set head
		$head = [
			'description' => $this->message['headDescription']
		];

		// set skin path
		$this->setSkinPath('form');

		$blade->render($this->skinAddr . '.form', [
			'root' => __GOOSE_ROOT__,
			'layout' => $layout,
			'util' => new core\Util(),
			'mod' => $this,
			'user' => $user,
			'head' => $head
		]);
	}

	/**
	 * login
	 *
	 * @param string $email
	 * @param string $password
	 * @param string $redir
	 * @param boolean $return
	 * @return boolean
	 *
	 */
	public function login($email='', $password='', $redir='', $return=false)
	{
		$user = core\Spawn::item([
			'table' => core\Spawn::getTableName('User'),
			'where' => "email='$email'"
		]);

		if ($user && $user['pw'] === md5($password))
		{
			$_SESSION['goose_srl'] = (int)$user['srl'];
			$_SESSION['goose_name'] = $user['name'];
			$_SESSION['goose_email'] = $user['email'];
			$_SESSION['goose_level'] = (int)$user['level'];
			$url = (strpos($redir, 'auth/login')) ? __GOOSE_ROOT__.'/' : $redir;
			if ($return)
			{
				return json_encode([
					'state' => 'success',
					'message' => 'login complete',
					'redir' => $redir
				]);
			}
			else
			{
				core\Util::redirect($url);
				return false;
			}
		}
		else
		{
			if ($return)
			{
				return json_encode([
					'state' => 'error',
					'message' => 'login fail'
				]);
			}
			else
			{
				core\Util::back('로그인정보가 맞지 않습니다.');
				return false;
			}
		}
	}

	/**
	 * logout
	 *
	 * @param string $url 로그아웃을 끝내고 이동하는 페이지 url
	 * @param boolean $return
	 * @return string
	 */
	public function logout($url=null, $return=false)
	{
		unset($_SESSION['goose_name']);
		unset($_SESSION['goose_email']);
		unset($_SESSION['goose_level']);

		if ($return)
		{
			return json_encode([
				'state' => 'success',
				'message' => 'success logout',
				'redir' => ($url) ? $url : __GOOSE_ROOT__.'/'
			]);
		}
		else
		{
			core\Util::redirect(($url) ? $url : __GOOSE_ROOT__.'/');
			return null;
		}
	}

	/**
	 * set skin path
	 *
	 * @param string $type
	 * @param string $userSkin
	 */
	private function setSkinPath($type, $userSkin=null)
	{
		// check blade file
		$bladeResult = core\Blade::isFile(__GOOSE_PWD__ . 'mod', $type, [
			$this->name . '.skin.' . $_GET['skin'],
			$this->name . '.skin.' . $userSkin,
			$this->name . '.skin.' . $this->set['skin'],
			$this->name . '.skin.default'
		]);

		// set blade and file path
		$this->skinAddr = $bladeResult['address'];
		$this->skinPath = 'mod/' . $bladeResult['path'] . '/';
	}
}