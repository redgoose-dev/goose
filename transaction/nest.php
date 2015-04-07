<?php
if(!defined("GOOSE")){exit();}

// check user level
if (!$goose->isAdmin)
{
	$goose->util->back("적용할 권한이 없습니다.");
	$goose->out();
}


// convert
$json = (isset($_POST['json'])) ? $goose->util->jsonToArray($_POST['json']) : null;

// load skin page
$skin = (isset($json['skin'])) ? $json['skin'] : $goose->user['skinDefault'];
$skin_path = '/plugins/nest/'.$skin;


if (is_file(PWD.$skin_path.'/transaction.php'))
{
	require_once(PWD.$skin_path.'/transaction.php');
	if ($redirectUrl)
	{
		$goose->util->redirect($redirectUrl);
	}
}
else if (is_file(PWD.'/plugins/nest/'.$goose->user['skinDefault'].'/transaction.php'))
{
	require_once(PWD.'/plugins/nest/'.$goose->user['skinDefault'].'/transaction.php');
	if ($redirectUrl)
	{
		$goose->util->redirect($redirectUrl);
	}
}
else
{
	$goose->util->alert('불러올 페이지가 없습니다.');
	$goose->out();
}
?>
