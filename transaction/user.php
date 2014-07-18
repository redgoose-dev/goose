<?
if(!defined("GOOSE")){exit();}

// email check
function checkExistEmail()
{
	global $spawn, $util, $tablesName;

	$cnt = $spawn->getCount(array(
		'table' => $tablesName['users'],
		'where' => "email='".$_POST['email']."'"
	));
	return ($cnt > 0) ? true : false;
}

switch($paramAction)
{
	case 'create':
		// check value
		$errorValue = $util->checkExistValue($_POST, array('name', 'email', 'pw', 'level'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}

		// create date
		$regdate = date("YmdHis");

		// 중복 이메일주소 감사
		if (checkExistEmail())
		{
			$util->back('이메일주소가 이미 존재합니다.');
			$util->out();
		}

		// insert data
		$result = $spawn->insert(array(
			'table' => $tablesName['users'],
			'data' => array(
				'srl' => null,
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'pw' => md5($_POST['pw']),
				'level' => (int)$_POST['level'],
				'regdate' => $regdate
			)
		));

		$util->redirect(ROOT.'/user/index/');
		break;

	case 'modify':
		// check value
		$errorValue = $util->checkExistValue($_POST, array('name', 'email', 'level'));
		if ($errorValue)
		{
			$util->back("[$errorValue]값이 없습니다.");
			$util->out();
		}
		
		if ($_SESSION['gooseEmail'] != $_POST['email'])
		{
			if ($_SESSION['gooseLevel'] < $adminLevel)
			{
				$util->back("수정할 수 있는 권한이 없습니다.");
				$util->out();
			}
		}

		// update db
		$result = $spawn->update(array(
			'table' => $tablesName['users'],
			'where' => 'srl='.(int)$_POST['user_srl'],
			'data' => array(
				"name='".$_POST['name']."'",
				"level='".$_POST['level']."'",
				($_POST['pw']) ? "pw='".md5($_POST['pw'])."'" : null
			)
		));

		$util->redirect(ROOT.'/user/index/');
		break;

	case 'delete':
		$result = $spawn->delete(array(
			'table' => $tablesName['users'],
			'where' => 'srl='.$_POST['user_srl']
		));

		$util->redirect(ROOT.'/user/index/');
		break;
}
?>
