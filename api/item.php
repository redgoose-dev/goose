<?php
if(!defined("GOOSE")){exit();}

require_once 'api/API.class.php';
require_once 'api/allowApiData.php';

$q = (count($_POST)) ? $_POST : $_GET;
$optputType = ($q['output']) ? $q['output'] : "html";
$data = array();

// api class init
$api = new API(array(
	'util' => $util
	,'tablesName' => $tablesName
	,'spawn' => $spawn
	,'apikey' => $api_key
	,'allow' => ((isset($allowApiData)) ? $allowApiData : null)
));

// check allowApiData
if (!isset($api->allow))
{
	$data['error'] = 'Empty allowApiData';
	echo $api->out($data, $optputType);
	$util->out();
}

// api key auth
$auth_result = $api->auth($q['apikey']);
if ($auth_result)
{
	$data['error'] = $auth_result;
	echo $api->out($data, $optputType);
	$util->out();
}

// action
switch($q['act'])
{
	case "index":
		$data = $api->getIndexItem(array(
			'table' => $_GET['table']
			,'field' => $_GET['field']
			,'page' => $_GET['page']
			,'limit' => $_GET['limit']
			,'order' => $_GET['order']
			,'sort' => $_GET['sort']

			,'where' => array(
				'srl' => $_GET['srl']
				,'id' => $_GET['id']
				,'group_srl' => $_GET['group_srl']
				,'nest_srl' => $_GET['nest_srl']
				,'article_srl' => $_GET['article_srl']
				,'category_srl' => $_GET['category_srl']
				,'thumnail_srl' => $_GET['thumnail_srl']
				,'key_srl' => $_GET['key_srl']
			)
			,'search_key' => $_GET['search_key']
			,'search_value' => $_GET['search_value']
		));
		break;

	case "single":
		$data = $api->getSingleItem(array(
			'table' => $_GET['table']
			,'field' => $_GET['field']
			,'key' => $_GET['key']
			,'value' => $_GET['value']
			,'search_key' => $_GET['search_key']
			,'search_value' => $_GET['search_value']
		));
		break;

	default:
		$data = array(
			'error' => "\"act\" parameter does not exist."
		);
		break;
}

$data = ($data) ? $data : array();

echo $api->out($data, $optputType);
$util->out();
?>