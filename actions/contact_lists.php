<?php

function updateContactLists()
{
	$tencentmail_key = TenCentDao::getSetting("tencentmail_key");
	$requestKey = $_REQUEST['tcmKey'];
	$contactLists = $_REQUEST['contactLists'];

	$response = (object)array();
	header('Content-type: application/json');

	if (!empty($contactLists) &&
		!empty($requestKey) &&
		$requestKey == $tencentmail_key
	) {

		try {

			TenCentDao::updateContactLists($contactLists);
			$updatedContactLists = TenCentDao::getAllContactLists();
			$response->contactLists = $updatedContactLists;
			$response->message = "Success";


		} catch (Exception $e) {
			header("HTTP/1.0 500 Error");
			$response->message = "Error";
		}

	} else {
		header("HTTP/1.0 400 Not Found");
		$response->message = "Resource not found";
	}

	echo json_encode($response);
	die();
}
