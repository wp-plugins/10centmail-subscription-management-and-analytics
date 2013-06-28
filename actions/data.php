<?php

function getData()
{
	$tencentmail_key = TenCentDao::getSetting("tencentmail_key");
	$requestKey = trim($_REQUEST['tcmKey']);

	$type = trim($_REQUEST['type']);
	$response = (object)array();

	header('Content-type: application/json');

	if (!empty($requestKey) &&
		!empty($tencentmail_key) &&
		isset($tencentmail_key) &&
		isset($requestKey) &&
		$tencentmail_key == $requestKey
	) {

		try {

			if ($type == null) $type = "all";

			$rawData = TenCentDao::data($type);
			$response->data = $rawData;
			$response->type = $type;
			$response->message = "Successfully retrieved '" . $type . "' data";

		} catch (Exception $e) {
			header("HTTP/1.0 500 Error");
			$response->message = "Error";
		}

	} else {
		header("HTTP/1.0 404 Not Found");
		$response->message = "Resource not found";
	}

	echo json_encode($response);
	die();

}
