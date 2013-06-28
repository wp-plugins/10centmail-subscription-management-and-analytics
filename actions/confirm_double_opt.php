<?php

function confirmDoubleOpt()
{
	$email = $_REQUEST['email'];
	$list = $_REQUEST['list'];

	if (isset($email) &&
		!empty($email) &&
		isset($list) &&
		!empty($list)
	) {
		try {
			TenCentDao::confirmOptIn($email, $list);

			$config = (object)array();
			$config->email = $email;
			$config->list = $list;

			$contactList = TenCentDao::getContactListByList($list);
			TenCentEmailer::sendDoubleOptEmails($config, $contactList);

			echo "<h1>" . TenCentDao::getSetting('tencentmail_company_name') . " : Opt In Confirmed</h1>";
			echo "<p>You have successfully confirmed to opt in to receive emails from us to your email at : $email</p>";

			$message = "<p>You have successfully confirmed to opt in to receive emails from us to your email at : $email</p>";
			$title = TenCentDao::getSetting('tencentmail_company_name') . " : Opt In Confirmed";
			wp_die($message, $title);
		} catch (Exception $e) {
			if (WP_DEBUG) {
				var_dump($e);
			}
			$error = "<p>Something went wrong when trying to confirm your opt in to receive emails at: $email</p><p>Please contact us at :" . TenCentDao::getSetting('tencentmail_company_contact') . "</p>";
			$title = TenCentDao::getSetting('tencentmail_company_name') . " : Failed to Confirm Opt In";
			wp_die($error, $title);

		}

	} else {
		if (WP_DEBUG) {
			echo 'values did not validate <br/>';
			echo '$email = ' . $email . '<br />';
			echo '$list = ' . $list . '<br />';
		}
		$error = "<p>Something went wrong when trying to confirm your opt in to receive emails at: $email</p><p>Please contact us at :" . TenCentDao::getSetting('tencentmail_company_contact') . "</p>";
		$title = TenCentDao::getSetting('tencentmail_company_name') . " : Failed to Confirm Opt In";
		wp_die($error, $title);
	}
}
