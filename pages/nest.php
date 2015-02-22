<?php
if(!defined("GOOSE")){exit();}


if ($paramAction == 'index')
{
	$group_srl = (isset($_SESSION['group_srl'])) ? (int)$_SESSION['group_srl'] : null;
	$group_srl = (isset($routePapameters['param0'])) ? (int)$routePapameters['param0'] : $group_srl;
	$group_srl = ($_GET['all']) ? null : $group_srl;

	$_SESSION['group_srl'] = $group_srl;

	$itemParameter = ($group_srl) ? 'group_srl='.$group_srl : '';
	$nestsAllCount = $goose->spawn->getCount(array('table'=>'nests'));
	$nestsCount = $goose->spawn->getCount(array('table'=>'nests', 'where'=>$itemParameter));
	$nestsIndex = $goose->spawn->getItems(array(
		'table' => 'nests',
		'where' => $itemParameter,
		'order' => 'srl',
		'sort' => 'desc'
	));
	$nestGroupsCount = $goose->spawn->getCount(array('table'=>'nestGroups'));

	require_once(PWD.'/pages/nest_index.php');
}
else
{
	// get nest data
	$nest_srl = (isset($routePapameters['param0'])) ? (int)$routePapameters['param0'] : null;
	if ($nest_srl)
	{
		$nest = $goose->spawn->getItem(array(
			'table' => 'nests',
			'where' => 'srl='.$nest_srl
		));
		$nest['json'] = ($nest['json']) ? json_decode(urldecode($nest['json']), true) : null;
	}

	// load skin page
	$skin = (isset($nest['json']['skin'])) ? $nest['json']['skin'] : $goose->user['skinDefault'];
	$skin = ($_GET['skin']) ? $_GET['skin'] : $skin;	
	$skin_path = '/plugins/nest/'.$skin;

	if (is_file(PWD.$skin_path.'/'.$paramAction.'.php'))
	{
		require_once(PWD.$skin_path.'/'.$paramAction.'.php');
	}
	else if (is_file(PWD.'/plugins/nest/'.$goose->user['skinDefault'].'/'.$paramAction.'.php'))
	{
		require_once(PWD.'/plugins/nest/'.$goose->user['skinDefault'].'/'.$paramAction.'.php');
	}
	else
	{
		$goose->util->alert('불러올 페이지가 없습니다.');
		$goose->out();
	}
}
?>
