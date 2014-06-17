<?php
if(!defined("GOOSE")){exit();}

require_once(PWD.'/libs/API.class.php');

$q = (count($_POST)) ? $_POST : $_GET;
$optputType = ($q[output]) ? $q[output] : "html";

// api class init
$api = new API(array(
	'util' => $util,
	'tablesName' => $tablesName,
	'spawn' => $spawn,
	'apikey' => $api_key
	
));

// api key auth
$auth_result = $api->auth($q[apikey]);
if ($auth_result)
{
	$data[error] = $auth_result;
	echo $api->out($data, $optputType);
	$util->out();
}

// action
switch($q[act])
{
	case "index":
		$data = $api->getIndexItem(array(
			table => $_GET[table],
			mod => $_GET[mod],
			group => $_GET[group],
			search => array($_GET[searchField], $_GET[searchKeyword]),
			page => $_GET[page],
			limit => $_GET[limit],
			field => $_GET[field],
			order => $_GET[order],
			sort => $_GET[sort]
		));
		break;

	case "single":
		$data = $api->getSingleItem(array(
			table => $_GET[table],
			key => $_GET[key],
			value => $_GET[value]
		));
		break;

	default:
		$data = array(
			error => "\"act\" parameter does not exist."
		);
		break;
}

echo $api->out($data, $optputType);
$util->out();
?>