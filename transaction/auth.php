<?
if(!defined("GOOSE")){exit();}

switch ($paramAction)
{
	case 'login':
		$pw = md5($_POST['password']);
		
		$auth = $spawn->getItem(array(
			field => '*',
			table => $tablesName[users],
			where => "email='". $_POST['email'] ."' and level='1'"
		));

		if ($auth[pw] == $pw)
		{
			$_SESSION['gooseEmail'] = $auth['email'];
			$_SESSION['gooseName'] = $auth['name'];
			$_SESSION['gooseLevel'] = $auth['level'];
			$util->redirect($_POST['redir']);
		}
		else
		{
			$util->back('비밀번호가 틀렸습니다.');
		}
		break;

	case 'logout':
		session_unset("gooseEmail");
		session_unset("gooseName");
		session_unset("gooseLevel");
		$util->redirect(ROOT."/");
		break;
}
?>