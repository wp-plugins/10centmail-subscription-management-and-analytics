<?php
$email = $_REQUEST['email'];
$listName = $_REQUEST['list'];
$campaignId = $_REQUEST['campaignId'];
?>

<html>
<head>
	<title><?php echo TenCentDao::getSetting('tencentmail_company_name') ?> : Unsubscribe Confirmation</title>
	<?php
	$css = "resources/css/unsubscribe.css";
	echo '<link rel="stylesheet" type="text/css" href="' . MY_PLUGIN_BASE_URL . $css . '"/>';
	?>
</head>
<body>
<div id="content">
	<?php

	if (isset($email) &&
		!empty($email) &&
		isset($listName) &&
		!empty($listName)) {

		try {

			$list = TenCentDao::getContactListByList($listName);

			if (!empty($list)) {

				$listSettings = TenCentDao::getContactListSettings($list->id);


				$pageContent = $listSettings['unsubscribe_page_content'];
				if (empty($pageContent)) echo "<h1>Unsubscribe</h1><p>Click the link below to Unsubscribe</p>";
				echo $pageContent;

				$buttonText = $listSettings['unsubscribe_button_text'];

				if (empty($buttonText)) $buttonText = "Unsubscribe";
				$requestUri = site_url() . '?10cent=unsubscribe';
				$params = "&email=$email&list=$list->list&campaignId=$campaignId";
				$requestUri .= $params;

				?>

				<a href="<?php echo $requestUri ?>" title="Submit Form"><?php echo $buttonText ?></a>

				<?php die(); ?>

			<?php
			} else {
				if (WP_DEBUG) {
					echo '$list was expected to not be empty <br/>';
					echo '$list = ' . $list . '<br />';
					echo '$email = ' . $email . '<br />';
					echo '$listName = ' . $listName . '<br />';
					echo '$campaignId = ' . $campaignId . '<br />';
				}

				$error = "<p>Something went wrong when trying to unsubscribe your email address at: $email</p><p>Please contact us at : " . TenCentDao::getSetting('tencentmail_support_email') . "</p>";
				$title = TenCentDao::getSetting('tencentmail_company_name') . " : Failed to Unsubscribe";
				wp_die($error, $title);
			}
		} catch (Exception $e) {
			if (WP_DEBUG) {
				var_dump($e);
			}
			$error = "<p>Something went wrong when trying to unsubscribe your email address at: $email</p><p>Please contact us at : " . TenCentDao::getSetting('tencentmail_support_email') . "</p>";
			$title = TenCentDao::getSetting('tencentmail_company_name') . " : Failed to Unsubscribe";
			wp_die($error, $title);
		}
	} else {
		if (WP_DEBUG) {
			echo 'input data was not valid <br/>';
			echo '$email = ' . $email . '<br />';
			echo '$listName = ' . $listName . '<br />';
			echo '$campaignId = ' . $campaignId . '<br />';
		}
		$error = "<p>Something went wrong when trying to unsubscribe your email address at: $email</p><p>Please contact us at : " . TenCentDao::getSetting('tencentmail_support_email') . "</p>";
		$title = TenCentDao::getSetting('tencentmail_company_name') . " : Failed to Unsubscribe";
		wp_die($error, $title);
	}?>

</div>
</body>
</html>