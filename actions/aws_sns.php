<?php

function processAwsSnsMessage()
{
	$log = new Logging();
	$response = (object)array();

	//if ($log) echo "logging works";

	$message = file_get_contents('php://input');
	$json = json_decode($message);

	$type = $json->Type;

	if ($type == "Notification") {

		$log->lwrite("NOTIFICATION -> ");

		foreach ($json as $n => $v) {
			$log->lwrite($n . " : " . $v);
		}

		$log->lwrite("MESSAGE : " . $message);

		// TenCentMailDAO::saveSnsMessage($type, $message);

	} else if ($type = "ConfirmSubscription") {

		$log->lwrite("SUBSCRIPTION CONFIRMATION -> ");

		$token = $json->Token;
		$topic = $json->TopicArn;

		$log->lwrite("\n\nTOKEN : " . $token);
		$log->lwrite("\n\nTOPIC : " . $topicArn);

		$topicArn = $topic;
		$request = "http://sns.us-east-1.amazonaws.com/";
		$request .= "?Action=ConfirmSubscription";
		$request .= "&TopicArn=" . $topicArn;
		$request .= "&Token=" . $token;

		$response = @file_get_contents($request);

		// TenCentMailDAO::saveSnsMessage($type, $message);

		$log->lwrite($response);

	}

	$log->lclose();
	$response->message = "aws sns";

	echo json_encode($response);

	die();

}
