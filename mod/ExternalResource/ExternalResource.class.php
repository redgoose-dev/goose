<?php
namespace mod\ExternalResource;
use core, mod;
if (!defined('__GOOSE__')) exit();


class ExternalResource {

	public $name, $set, $params, $isAdmin;
	public $path, $skinPath, $skinAddr, $skinUrl, $ftp;

	public function __construct($params)
	{
		core\Module::initModule($this, $params);

		$this->skinAddr = $this->name . '.app';
		$this->skinPath = $this->path . 'app/';
		$this->skinUrl = __GOOSE_ROOT__ . '/' . $this->name;
		$this->ftp = null;
	}

	/**
	 * index
	 */
	public function index()
	{
		// get ftp connect information
		$this->loadFtpInfo();

		if ($this->params['method'] == 'POST')
		{
			// POST

			switch($this->param['action'])
			{
				case 'updateFTP':
					$result = $this->updateFtp();
					core\Util::redirect((isset($_POST['redir'])) ? $_POST['redir'] : $this->url.'/', $result['message']);
					break;

				case 'testFTP':
					$result = $this->testFtp([
						'host' => $_POST['host_name'],
						'id' => $_POST['host_id'],
						'pw' => $_POST['host_pw'],
						'pwd' => $_POST['host_pwd'],
					]);
					echo core\Util::arrayToJson($result, false, false);
					break;

				case 'install':
					echo core\Util::arrayToJson($this->installContent(), false, false);
					break;

				case 'checkInstallLocation':
					$loc = str_replace('{GOOSE}/', __GOOSE_PWD__, $_POST['location']);
					echo core\Util::arrayToJson([ 'overlap' => (is_dir($loc)) ], false, false);
					break;
			}
		}
		else
		{
			// GET

			switch($this->params['action'])
			{
				case 'setting':
					$view = new View($this);
					$view->view_setting();
					break;

				default:
					$view = new View($this);
					$view->view_index();
					break;

				case 'checkInstall':
					if ($this->ftp)
					{
						$result = $this->testFtp([
							'host' => $this->ftp['host'],
							'id' => $this->ftp['id'],
							'pw' => $this->ftp['password'],
							'pwd' => $this->ftp['pwd'],
						]);
					}
					else
					{
						$result = [
							'state' => 'error',
							'message' => 'not found ftp setting',
						];
					}
					echo core\Util::arrayToJson($result, false, false);
					break;
			}
		}
	}

//	/**
//	 * view - index
//	 *
//	 * @param string $fileName
//	 */
//	private function view_index($fileName)
//	{
//		// set pwd_container
//		$this->pwd_container = __GOOSE_PWD__.$this->viewPath.$fileName.'.html';
//
//		require_once($this->layout->getUrl());
//	}

	/**
	 * test ftp
	 *
	 * @param array $connInf connection info
	 * @return array
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
			if (true !== $loggedIn)
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
					'message' => 'Success connect!',
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
	 * @return array
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

				$result = core\Util::fop(__GOOSE_PWD__.'data/ftp.php', 'w', $str, 0755);

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
	 * load ftp information
	 */
	private function loadFtpInfo()
	{
		$ftp_account = null;
		if (file_exists(__GOOSE_PWD__ . 'data/ftp.php'))
		{
			require_once(__GOOSE_PWD__ . 'data/ftp.php');
			$this->ftp = $ftp_account;
		}
	}

	/**
	 * install content
	 *
	 * @return array
	 */
	private function installContent()
	{
		$result = [];

		try {

			$file_ftpSetting = __GOOSE_PWD__.'data/ftp.php';

			// check value $_POST
			$errorValue = core\Util::checkExistValue($_POST, ['install_file', 'pwd']);
			if ($errorValue)
			{
				throw new Exception('not found ['.$errorValue.']');
			}

			// check content file
			if (file_exists($_POST['install_file']))
			{
				throw new Exception('not found content file');
			}

			$_POST['pwd'] = str_replace('{GOOSE}/', '', $_POST['pwd']);

			// set temp filename
			$tmp_filename = 'tmp-install.zip';

			// set install location
			$dest_loc = explode('/', $_POST['pwd']);
			$dest_name = array_pop($dest_loc);
			$dest_loc = implode('/', $dest_loc) . '/';
			$dest_pwd = __GOOSE_PWD__.$dest_loc;

			$file_loc = explode('/', $_POST['install_file']);
			$file_name = array_pop($file_loc);
			$dest_dir = $this->ftp['pwd'].$dest_loc.$dest_name;

			// check ftp setting file
			if (!file_exists($file_ftpSetting))
			{
				throw new Exception('not found ftp setting file');
			}

			$conn_id = ftp_connect($this->ftp['host']);
			$login_result = ftp_login($conn_id, $this->ftp['id'], $this->ftp['password']);

			if ((!$conn_id) || (!$login_result))
			{
				throw new Exception("FTP connection has failed!\nAttempted to connect to ".$this->ftp['host']." for user ".$this->ftp['id']);
			}

			if (ftp_chdir($conn_id, $dest_dir))
			{
				throw new Exception("같은 이름의 디렉토리가 있습니다.\n[".$dest_dir."]\n경로를 확인해주세요.");
			}

			// make directory
			if (!ftp_mkdir($conn_id, $dest_dir))
			{
				throw new Exception("Can't make [$dest_dir]directory");
			}

			// upload file
			if (ftp_put($conn_id, $dest_dir.'/'.$tmp_filename, $_POST['install_file'], FTP_BINARY) === FALSE)
			{
				throw new Exception('upload error');
			}

			// unpack tmpfile
			$zip = new ZipArchive;
			if ($zip->open($dest_pwd.$dest_name.'/'.$tmp_filename) === TRUE)
			{
				$zip->extractTo($dest_pwd.$dest_name);
				$zip->close();
			}
			else
			{
				throw new Exception('Extract zip compression failure');
			}

			// remove tmpfile
			unlink($dest_pwd.$dest_name.'/'.$tmp_filename);

			// edit dest dir
			$dest_dir = str_replace($this->ftp['pwd'], '{GOOSE}/', $dest_dir);

			// goal
			return [
				'state' => 'success',
				'message' => "설치 완료되었습니다.\n\n설치경로:\n".$dest_dir."",
			];

		} catch(Exception $e) {
			return [
				'state' => 'error',
				'message' => $e->getMessage(),
			];
		}
	}
}
