<?php

function unsubscribe()
{
	$email = $_REQUEST['email'];
	$listName = $_REQUEST['list'];
	$campaignId = $_REQUEST['campaignId'];

	if (isset($email) &&
		!empty($email) &&
		isset($listName) &&
		!empty($listName)
	) {

		try {
			$c = 0;
			if (!empty($campaignId) && isset($campaignId))
				$c = intval($campaignId);

			$result = TenCentDao::unsubscribe($email, $listName, $c);
			$list = TenCentDao::getContactListByList($listName);

			if (empty($result)) {
				if (WP_DEBUG) {
					echo '$result was expected to not be empty <br/>';
					echo '$email = ' . $email . '<br />';
					echo '$listName = ' . $listName . '<br />';
					echo '$campaignId = ' . $campaignId . '<br />';
					var_dump($list);
				}
				$title = "Unsubscribe Failed";
				$message = "<h2>Something went wrong</h2><p>Please contact us at " . TenCentDao::getSetting("tencentmail_support_email") . "</p>";
				wp_die($message, $title);
			} else {

				if (!empty($list)) {

					$listSettings = TenCentDao::getContactListSettings($list->id);
					$pageContent = $listSettings['unsubscribe_success_message'];

					$title = "Successfully Unsubscribed";
					if (empty($pageContent)) $pageContent = "<h1>Successfully Unsubscribed</h1>";

					wp_die($pageContent, $title);
				}
			}
		} catch (Exception $e) {
			if (WP_DEBUG) {
				var_dump($e);
			}
			$title = "Failed to unsubscribe";
			$message = "<h2>Something went wrong</h2><p>Please contact us at " . TenCentDao::getSetting("tencentmail_support_email") . "</p>";

			wp_die($message, $title);
		}
	} else {
		if (WP_DEBUG) {
			echo 'values were not valid <br/>';
			echo '$email = ' . $email . '<br />';
			echo '$listName = ' . $listName . '<br />';
			echo '$campaignId = ' . $campaignId . '<br />';
		}
		$title = "Failed to unsubscribe";
		$message = "<h2>Something went wrong</h2><p>Please contact us at " . TenCentDao::getSetting("tencentmail_support_email") . "</p>";

		wp_die($message, $title);
	}
}
