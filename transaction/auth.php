<?
if(!defined("GOOSE")){exit();}

switch ($paramAction)
{
	case 'login':
		$pw = md5($_POST['password']);
		
		$auth = $goose->spawn->getItem(array(
			'field' => '*',
			'table' => 'users',
			'where' => "email='".$_POST['email']."'"
		));

		if ($auth['pw'] == $pw)
		{
			if ($auth['level'] > $goose->user['loginLevel'])
			{
				$goose->util->back('접속 권한이 없습니다.');
			}
			else
			{
				$_SESSION['gooseEmail'] = $auth['email'];
				$_SESSION['gooseName'] = $auth['name'];
				$_SESSION['gooseLevel'] = $auth['level'];
				$goose->util->redirect($_POST['redir']);
			}
		}
		else
		{
			$goose->util->back('비밀번호가 틀렸습니다.');
		}
		break;

	case 'logout':
		session_unset("gooseEmail");
		session_unset("gooseName");
		session_unset("gooseLevel");
		$goose->util->redirect(GOOSE_ROOT."/");
		break;
}
?>