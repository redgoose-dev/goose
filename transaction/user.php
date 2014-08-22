<?
if(!defined("GOOSE")){exit();}

// email check
function checkExistEmail()
{
	global $goose;

	$cnt = $goose->spawn->getCount(array(
		'table' => 'users',
		'where' => "email='".$_POST['email']."'"
	));
	return ($cnt > 0) ? true : false;
}

switch($paramAction)
{
	case 'create':
		// check value
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'email', 'pw', 'level'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}

		// create date
		$regdate = date("YmdHis");

		// 중복 이메일주소 감사
		if (checkExistEmail())
		{
			$goose->util->back('이메일주소가 이미 존재합니다.');
			$goose->out();
		}

		// insert data
		$result = $goose->spawn->insert(array(
			'table' => 'users',
			'data' => array(
				'srl' => null,
				'name' => $_POST['name'],
				'email' => $_POST['email'],
				'pw' => md5($_POST['pw']),
				'level' => (int)$_POST['level'],
				'regdate' => $regdate
			)
		));

		$goose->util->redirect(GOOSE_ROOT.'/user/index/');
		break;

	case 'modify':
		// check value
		$errorValue = $goose->util->checkExistValue($_POST, array('name', 'email', 'level'));
		if ($errorValue)
		{
			$goose->util->back("[$errorValue]값이 없습니다.");
			$goose->out();
		}
		
		if ($_SESSION['gooseEmail'] != $_POST['email'])
		{
			if ($_SESSION['gooseLevel'] < $adminLevel)
			{
				$goose->util->back("수정할 수 있는 권한이 없습니다.");
				$goose->out();
			}
		}

		// update db
		$result = $goose->spawn->update(array(
			'table' => 'users',
			'where' => 'srl='.(int)$_POST['user_srl'],
			'data' => array(
				"name='".$_POST['name']."'",
				"level='".$_POST['level']."'",
				($_POST['pw']) ? "pw='".md5($_POST['pw'])."'" : null
			)
		));

		$goose->util->redirect(GOOSE_ROOT.'/user/index/');
		break;

	case 'delete':
		$result = $goose->spawn->delete(array(
			'table' => 'users',
			'where' => 'srl='.$_POST['user_srl']
		));

		$goose->util->redirect(GOOSE_ROOT.'/user/index/');
		break;
}
?>
