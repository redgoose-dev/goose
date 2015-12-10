<?php
if (!defined('__GOOSE__')) exit();

/**
 * Module - resource
 */
class Resource {

	public $goose, $param, $set, $name, $layout, $isAdmin, $method;
	public $path, $skinPath, $pwd_container;

	/**
	 * construct
	 *
	 * @param array $getter
	 */
	public function __construct($getter=array())
	{
		$this->name = $getter['name'];
		$this->goose = $getter['goose'];
		$this->isAdmin = $getter['isAdmin'];
		$this->param = $getter['param'];
		$this->path = $getter['path'];
		$this->set = $getter['set'];

		$this->viewPath = $this->path.'app/';
		$this->url = __GOOSE_ROOT__.'/'.$this->name;
		$this->ftp = null;
	}

	/**
	 * index
	 */
	public function index()
	{
		// set layout module
		$this->layout = Module::load('layout');

		$this->loadFtpInfo();

		if ($this->param['method'] == 'POST')
		{
			switch($this->param['action'])
			{
				case 'updateFTP':
					$result = $this->updateFtp();
					Util::redirect((isset($_POST['redir'])) ? $_POST['redir'] : $this->url.'/', $result['message']);
					break;

				case 'testFTP':
					$result = $this->testFtp([
						'host' => $_POST['host_name'],
						'id' => $_POST['host_id'],
						'pw' => $_POST['host_pw'],
						'pwd' => $_POST['host_pwd'],
					]);
					echo json_encode($result);
					break;

				case 'install':
					var_dump('install content');
					var_dump($_POST);
					break;
			}
		}
		else
		{
			switch($this->param['action'])
			{
				case 'setting':
					$this->view_index('view_setting');
					break;

				default:
					$this->view_index('view_index');
					break;
			}
		}
	}

	/**
	 * view - index
	 *
	 * @param string $fileName
	 */
	private function view_index($fileName)
	{
		// set pwd_container
		$this->pwd_container = __GOOSE_PWD__.$this->viewPath.$fileName.'.html';

		require_once($this->layout->getUrl());
	}

	/**
	 * test ftp
	 *
	 * @param array $connInf connection info
	 * @return boolean
	 */
	private function testFtp($connInf)
	{
		try {
			$con = ftp_connect($connInf['host']);
			if (false === $con)
			{
				throw new Exception('Unable to connect');
			}
			$loggedIn = ftp_login($con, $connInf['id'], $connInf['pw']);
			if (true === $loggedIn) {}
			else
			{
				throw new Exception('Unable to login');
			}
			$connInf['pwd'] .= (!preg_match('/\/$/', $connInf['pwd'])) ? '/' : '';
			$result = ftp_nlist($con, $connInf['pwd'].'core/init.php');
			ftp_close($con);

			if (is_array($result) && count($result))
			{
				return [
					'state' => 'success',
					'message' => 'success connect',
				];
			}
			else
			{
				throw new Exception('The path is incorrect.');
			}
		}
		catch (Exception $e)
		{
			return [
				'state' => 'error',
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * update ftp
	 *
	 * @param array $connInf connection info
	 * @return boolean
	 */
	private function updateFtp()
	{
		function var_export54($var, $indent="") {
			switch (gettype($var)) {
				case "string":
					return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
				case "array":
					$indexed = array_keys($var) === range(0, count($var) - 1);
					$r = [];
					foreach ($var as $key => $value) {
						$r[] = "$indent	"
								. ($indexed ? "" : var_export54($key) . " => ")
								. var_export54($value, "$indent	");
					}
					return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
				case "boolean":
					return $var ? "TRUE" : "FALSE";
				default:
					return var_export($var, TRUE);
			}
		}

		$result = null;
		$testResult = $this->testFtp([
			'host' => $_POST['host_name'],
			'id' => $_POST['host_id'],
			'pw' => $_POST['host_pw'],
			'pwd' => $_POST['host_pwd'],
		]);

		try {
			if ($testResult['state'] == 'success')
			{
				$ftp_account = [
					"host" => $_POST['host_name'],
					"id" => $_POST['host_id'],
					"password" => $_POST['host_pw'],
					"pwd" => (preg_match('/\/$/', $_POST['host_pwd'])) ? $_POST['host_pwd'] : $_POST['host_pwd'].'/'
				];

				$str = "<?php\nif (!defined('__GOOSE__')) exit();\n\n";
				$str .= '$ftp_account = '.var_export54($ftp_account, '').';';

				$result = Util::fop(__GOOSE_PWD__.'data/ftp.php', 'w', $str, 0755);

				if ($result != 'success')
				{
					throw new Exception('Fail write ftp file');
				}
			}
			else
			{
				throw new Exception('Fail test ftp');
			}
			return [
				'state' => 'success',
				'message' => 'update complete',
			];
		}
		catch (Exception $e)
		{
			return [
				'state' => 'error',
				'message' => $e->getMessage(),
			];
		}
	}

	/**
	 * load ftp info
	 *
	 * @return void
	 */
	private function loadFtpInfo()
	{
		$ftp_account = null;
		if (file_exists(__GOOSE_PWD__.'data/ftp.php'))
		{
			require_once(__GOOSE_PWD__.'data/ftp.php');
			$this->ftp = $ftp_account;
		}
	}

}