<?php
define('PUN_ROOT', '../');
require PUN_ROOT.'include/common.php';
header('Content-type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
/*$url = preg_replace('{/$}', '', $_SERVER['REQUEST_URI']);
$url_path = parse_url($url, PHP_URL_PATH);
$query_string = parse_url($url, PHP_URL_QUERY);
$url_path_explode = explode('/', $url_path);
$url_end = end($url_path_explode);*/

$context = $_GET['context'];

if (!file_exists(dirname(__FILE__).'/api.'.$context.'.php')){
	http_response_code(404);
	exit(0);
}


if (!isset($method) || $method == ''){
	http_response_code(404);	
	exit(0);
}

include('api.'.$context.'.php');

handleMethod($method);

//Api utils functions
function handleMethod($method){
	switch ($method) {
		case 'GET':
			if(function_exists('doGet')){
				$returnValue = doGet($_GET);
			}
			break;
		
		case 'POST':
			if(function_exists('doPost')){
				$returnMessage = doPost($_POST);
			}
			break;

		case 'PUT':
			if(function_exists('doPut')){
				$returnValue = doPut();
			}
			break;

		case 'DELETE':
			if(function_exists('doDelete')){
				$returnValue = doDelete($_GET);
			}
			break;

		default:
			http_response_code(404);
			exit(0);		
			break;
	}
	//JSON Response
	if ($returnValue){
		echo buildReturnValue($returnValue);		
	}

	if ($returnMessage){
		echo buildReturnMessage($returnMessage);
	}
}

function apiDbLayerError($message){
	// Send no-cache headers
	header('Expires: Thu, 21 Jul 1977 07:30:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: post-check=0, pre-check=0', false);
	header('Pragma: no-cache');

	echo buildReturnMessage(['error', $message]);
	exit(0);
}

function buildReturnMessage($array){
	$result = $array[0];
	$message = $array[1];
	$message = json_encode(array('result' => $result, 'message' => $message), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
	return $message;
}

function buildReturnValue($returnValue){
	if (!is_string($returnValue))
		return json_encode($returnValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	
	return $returnValue;
}
?>