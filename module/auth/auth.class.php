<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - Auth
 */
class Auth {

	public $goose, $set, $layout;
	public $skinPath, $pwd_container, $path;
	public $message;

	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];
	}

	/**
	 * index method
	 */
	public function index()
	{
		switch($this->param['action'])
		{
			case "login":
				if ($this->param['method'] == 'POST')
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
		}
	}

	/**
	 * auth
	 *
	 * @param int $level goose에 접근할 수 있는 레벨값
	 */
	public function auth($level=0)
	{
		if ($this->param['action'] == 'logout')
		{
			self::index();
		}
		else if ($level && ($level > $_SESSION['goose_level']))
		{
			$this->param['action'] = 'login';
			if ($level && $_SESSION['goose_level'])
			{
				$this->message['headDescription'] = '접근 권한이 없습니다.';
			}
			self::index();
			Goose::end();
		}
	}

	/**
	 * login form
	 */
	public function loginForm()
	{
		// set view path
		$this->skinPath = $this->path.'skin/'.$this->set['skin'].'/';

		// set container path
		$this->pwd_container = __GOOSE_PWD__.$this->skinPath.'login-form.html';

		// set layout module print
		$this->layout = Module::load('layout');
		require_once($this->layout->getUrl());
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
		$user = Spawn::item(array(
			'table' => Spawn::getTableName('user')
			,'where' => "email='$email'"
		));

		if ($user && $user['pw'] === md5($password))
		{
			$_SESSION['goose_name'] = $user['name'];
			$_SESSION['goose_email'] = $user['email'];
			$_SESSION['goose_level'] = $user['level'];
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
				Util::redirect($url);
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
				Util::back('로그인정보가 맞지 않습니다.');
				return false;
			}
		}
	}

	/**
	 * logout
	 *
	 * @param string $url 로그아웃을 끝내고 이동하는 페이지 url
	 * @return boolean
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
			Util::redirect(($url) ? $url : __GOOSE_ROOT__.'/');
			return false;
		}
	}

}