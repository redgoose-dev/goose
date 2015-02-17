<?php
if(!defined("GOOSE")){exit();}

require_once 'api/API.class.php';
require_once 'api/allowApiData.php';

$q = (count($_POST)) ? $_POST : $_GET;
$optputType = ($q['output']) ? $q['output'] : "html";
$data = array();


// api class init
$api = new API(array(
	'util' => $goose->util
	,'tablesName' => $goose->tablesName
	,'spawn' => $goose->spawn
	,'apikey' => $goose->api_key
	,'allow' => ((isset($allowApiData)) ? $allowApiData : null)
));

// check allowApiData
if (!isset($api->allow))
{
	$data['error'] = 'Empty allowApiData';
	echo $api->output($data, $optputType);
	$util->out();
}

// api key auth
$auth_result = $api->auth($q['apikey']);
if ($auth_result)
{
	$data['error'] = $auth_result;
	echo $api->output($data, $optputType);
	$goose->out();
}

// action
switch($q['act'])
{
	case "index":
		$data = $api->getIndexItem(array(
			'table' => $q['table']
			,'field' => $q['field']
			,'page' => $q['page']
			,'limit' => $q['limit']
			,'order' => $q['order']
			,'sort' => $q['sort']

			,'where' => array(
				'srl' => $q['srl']
				,'id' => $q['id']
				,'group_srl' => $q['group_srl']
				,'nest_srl' => $q['nest_srl']
				,'article_srl' => $q['article_srl']
				,'category_srl' => $q['category_srl']
				,'thumnail_srl' => $q['thumnail_srl']
				,'key_srl' => $q['key_srl']
			)
			,'search_key' => $q['search_key']
			,'search_value' => $q['search_value']
		));
		break;

	case "single":
		$data = $api->getSingleItem(array(
			'table' => $q['table']
			,'field' => $q['field']
			,'key' => $q['key']
			,'value' => $q['value']
			,'search_key' => $q['search_key']
			,'search_value' => $q['search_value']
		));
		break;

	default:
		$data = array(
			'error' => "\"act\" parameter does not exist."
		);
		break;
}

$data = ($data) ? $data : array();

echo $api->output($data, $optputType);
$goose->out();
?>