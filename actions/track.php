<?php

function track()
{
	define("IMAGES_DIR", plugins_url() . "/tencentmail/resources/images/");
	define("TEST_IMAGE", "test.png");
	define("PRODUCTION_IMAGE", "production.png");

	$test = false;

	$type = $_REQUEST['type'];
	$trackingId = $_REQUEST['trackingId'];
	$url = $_REQUEST['url'];


	if (!empty($type) &&
		!empty($trackingId)
	) {

		$ip = $_SERVER['REMOTE_ADDR'];;
		$agent = $_SERVER['HTTP_USER_AGENT'];
		$referer = $_SERVER['HTTP_REFERER'];

		if ($type == "image") {

			$image_source = IMAGES_DIR . PRODUCTION_IMAGE;
			if ($test) $image_source = IMAGES_DIR . TEST_IMAGE;

			TenCentDAO::saveTrackingId($trackingId, $type, $url, $ip, $agent, $referer);

			$im = file_get_contents($image_source);

			header('content-type: image/png');

			echo $im;

			die();

		} elseif ($type == "link") {

			if ($url != "") {

				$decoded = urldecode($url);

				$urlComponents = parse_url($decoded);

				if (!empty($urlComponents)) {
					$newLocation = $decoded;
					$scheme = $urlComponents["scheme"];
					if (empty($scheme)) {
						$newLocation = "http://" . $decoded;
					}

					TenCentDAO::saveTrackingId($trackingId, $type, $url, $ip, $agent, $referer);

					header("HTTP/1.0 302 Found");
					header('Location: ' . $newLocation);
				} else {
					TenCentDAO::saveTrackingId($trackingId, $type, "INVALID_URL:" . $url, $ip, $agent, $referer);
				}
			} else {
				TenCentDAO::saveTrackingId($trackingId, $type, "EMPTY_URL", $ip, $agent, $referer);
				wp_die("Resource Not Found");
			}
		} else {
			wp_die("Resource Not Found");
		}
	} else {
		wp_die("Resource Not Found");
	}

	die();
}
