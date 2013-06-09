<?php

function getEndpoints()
{

	$tencentmail_key = TenCentDao::getSetting("tencentmail_key");
	$requestKey = $_REQUEST['tcmKey'];

	$endpoints = (object)array();
	$response = (object)array();

	header('Content-type: application/json');

	if ($requestKey != "" &&
		$tencentmail_key != "" &&
		isset($tencentmail_key) &&
		isset($requestKey) &&
		$tencentmail_key == $requestKey
	) {

		$baseUrl = site_url();

		$endpoints->data = $baseUrl . "?10cent=data";
		$endpoints->track = $baseUrl . "?10cent=track";
		$endpoints->unsubscribe = $baseUrl . "?10cent=unsubscribe";
		$endpoints->unsubscribe_form = $baseUrl . "?10cent=unsubscribe_form";
		$endpoints->acknowledge = $baseUrl . "?10cent=acknowledge";
		$endpoints->contactLists = $baseUrl . "?10cent=contact_lists";

		$response->message = "success";
		$response->endpoints = $endpoints;


	} else {
		header("HTTP/1.0 400 Not Found");
		$response->message = "Resource not found";
	}

	echo json_encode($response);
	die();

}
