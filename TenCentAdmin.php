<?php

function tcm_settings_page()
{
	$css = "resources/css/admin.css";
	echo '<link rel="stylesheet" type="text/css" href="' . MY_PLUGIN_BASE_URL . $css . '"/>';

	$id = "";
	$list = "";
	$skipList = false;
	$inactive = false;
	$keyDivClass = "";
	$keyHeaderClass = "";
	$key = TenCentDao::getSetting("tencentmail_key");
	$contactLists = TenCentDao::getAllContactLists();
	$data = TenCentDao::data(null);

	if (empty($key)) {
		$keyDivClass = "critical";
		$keyHeaderClass = "critical-setting";
	}

	if (isset($_REQUEST['list'])) {
		$list = $_REQUEST['list'];
	}

	if (isset($_REQUEST['settings-form']) &&
		$_REQUEST['settings-form'] == "DELETE_CONTACT_LIST"
	) {
		$skipList = true;
	}

	$contactListDropDownOptions = '';
	foreach ($contactLists as $contactList) {

		if ($skipList && $contactList->list == $list) break;

		if ($list == $contactList->list) {
			$id = $contactList->id;
			if ($contactList->active == Utils::FALSE) $inactive = true;
		}
		$activeString = $contactList->active == Utils::TRUE ? '' : '<span class="inactive">- inactive</span>';
		$contactListDropDownOptions .= '<option value="' . $contactList->list . '">' . $contactList->list . $activeString . '</option>';

	}

	$listSettings = array();
	if (!empty($id)) {
		$listSettings = TenCentDao::getContactListSettings($id);
	}

	if (empty($listSettings)) {
		$listSettings = array(
			"listId" => "",
			"unsubscribe_link_text" => "",
			"unsubscribe_button_text" => "",
			"unsubscribe_page_content" => "",
			"unsubscribe_success_message" => "",
			"subscribe_button_text" => "",
			"subscribe_success_message" => "",
			"thank_you_subscribe_subject" => "",
			"thank_you_subscribe_message" => "",
			"double_opt_in_confirmation_subject" => "",
			"double_opt_in_confirmation_link_content" => "",
			"thank_you_double_opt_in_subject" => "",
			"thank_you_double_opt_in_message" => ""
		);
	}
	?>

	<div class="wrap tencentmail-settings-wrapper">

	<div class="wrap tencentmail-settings" id="critical">

	<div class="icon32" id="icon-options-general"><br></div>

	<h2>10CentMail Settings</h2>

	<!--	<p>Welcome to the administration panel of 10CentMail.</p>-->


	<?php

	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {

		if ($_POST['settings-form'] == "DELETE_CONTACT_LIST") {

			if (!empty($_POST['list']) &&
				!empty($_POST['id'])
			) {
				TenCentDao::removeEverythingForContactList($_POST['id']);
				echo '<div class="updated fade" id="successMessage"><p>Successfully deleted ' . $_POST['list'] . '</p></div>';
			}

		} else {

			if ($_POST['settings-form'] == "BASE_EMAIL_SETTINGS") {

				if (!empty($_POST['tencentmail_key'])) {

					TenCentDao::updateSetting("tencentmail_key", $_POST['tencentmail_key']);
					echo '<div class="updated fade" id="successMessage"><p>Successfully saved Key</p></div>';

					$keyDivClass = "";
					$keyHeaderClass = "";

				} else {
					echo '<div class="error alternate fade"><p>Please enter a 10CentMail Key</p></div>';
				}

				if (!empty($_POST['tencentmail_company_name'])) {
					TenCentDao::updateSetting("tencentmail_company_name", $_POST['tencentmail_company_name']);
					echo '<div class="updated fade" id="successMessage"><p>Updated Company Name</p></div>';
				} else {
					echo '<div class="error fade"><p>Company Name is Empty</p></div>';
				}

				if (!empty($_POST['tencentmail_support_email'])) {
					TenCentDao::updateSetting("tencentmail_support_email", $_POST['tencentmail_support_email']);
					echo '<div class="updated fade" id="successMessage"><p>Updated Support Email</p></div>';
				} else {
					echo '<div class="error fade"><p>Support Email is Empty</p></div>';
				}

				if (!empty($_POST['tencentmail_from_email'])) {
					TenCentDao::updateSetting("tencentmail_from_email", $_POST['tencentmail_from_email']);
					echo '<div class="updated fade" id="successMessage"><p>Updated From Email</p></div>';
				} else {
					echo '<div class="error fade"><p>From Email is Empty</p></div>';
				}

				if (!empty($_POST['tencentmail_from_name'])) {
					TenCentDao::updateSetting("tencentmail_from_name", $_POST['tencentmail_from_name']);
					echo '<div class="updated fade" id="successMessage"><p>Updated From Name</p></div>';
				} else {
					echo '<div class="error fade"><p>From Name is Empty</p></div>';
				}

				if (!empty($_POST['tencentmail_notification_emails'])) {
					TenCentDao::updateSetting("tencentmail_notification_emails", $_POST['tencentmail_notification_emails']);
					echo '<div class="updated fade" id="successMessage"><p>Updated Notification Emails</p></div>';
				} else {
					echo '<div class="error fade"><p>Notification Emails is Empty</p></div>';
				}

			}

			if ($_POST['settings-form'] == "CONTACT_LIST_SETTINGS") {

				$saveSettingsObj = array();

				//SUBSCRIBE FORM SETTINGS
				if (empty($_POST["subscribe_button_text"])) {
					echo '<div class="error fade"><p>Subscribe Button Text is empty</p></div>';
				} else {
					$saveSettingsObj["subscribe_button_text"] = $_POST["subscribe_button_text"];
				}

				if (empty($_POST["subscribe_success_message"])) {
					echo '<div class="error fade"><p>Subscribe Message is empty</p></div>';
				} else {
					$saveSettingsObj["subscribe_success_message"] = $_POST["subscribe_success_message"];
				}

				//UNSUBSCRIBE FORM SETTINGS
				if (empty($_POST["unsubscribe_link_text"])) {
					echo '<div class="error fade"><p>Unsubscribe Link Text is Empty</p></div>';
				} else {
					$saveSettingsObj["unsubscribe_link_text"] = $_POST["unsubscribe_link_text"];
				}

				if (empty($_POST["unsubscribe_button_text"])) {
					echo '<div class="error fade"><p>Unsubscribe Button Text is empty</p></div>';
				} else {
					$saveSettingsObj["unsubscribe_button_text"] = $_POST["unsubscribe_button_text"];
				}

				if (empty($_POST["unsubscribe_page_content"])) {
					echo '<div class="error fade"><p>Unsubscribe Page Content is empty</p></div>';
				} else {
					$saveSettingsObj["unsubscribe_page_content"] = $_POST["unsubscribe_page_content"];
				}

				if (empty($_POST["unsubscribe_success_message"])) {
					echo '<div class="error fade"><p>Unsubscribe Success page content is empty</p></div>';
				} else {
					$saveSettingsObj["unsubscribe_success_message"] = $_POST["unsubscribe_success_message"];
				}

				//THANK YOU SUBSCRIBE SETTINGS
				if (empty($_POST["thank_you_subscribe_subject"])) {
					echo '<div class="error fade"><p>Thank you subject is empty</p></div>';
				} else {
					$saveSettingsObj["thank_you_subscribe_subject"] = $_POST["thank_you_subscribe_subject"];
				}

				if (empty($_POST["thank_you_subscribe_message"])) {
					echo '<div class="error fade"><p>Thank you message is empty</p></div>';
				} else {
					$saveSettingsObj["thank_you_subscribe_message"] = $_POST["thank_you_subscribe_message"];
				}

				//Double Opt Confirmation Settings
				if (empty($_POST["double_opt_in_confirmation_subject"])) {
					echo '<div class="error fade"><p>Double Opt-In subject is empty</p></div>';
				} else {
					$saveSettingsObj["double_opt_in_confirmation_subject"] = $_POST["double_opt_in_confirmation_subject"];
				}

				if (empty($_POST["double_opt_in_confirmation_link_content"])) {
					echo '<div class="error fade"><p>The Double Opt-In link content is blank</p></div>';
				} else {
					$saveSettingsObj["double_opt_in_confirmation_link_content"] = $_POST["double_opt_in_confirmation_link_content"];
				}

				//Thank You Double Opt Confirmation Settings
				if (empty($_POST["thank_you_double_opt_in_subject"])) {
					echo '<div class="error fade"><p>Thank You Opt-In subject is empty</p></div>';
				} else {
					$saveSettingsObj["thank_you_double_opt_in_subject"] = $_POST["thank_you_double_opt_in_subject"];
				}

				if (empty($_POST["thank_you_double_opt_in_message"])) {
					echo '<div class="error fade"><p>Thank You for Opt-In message is empty</p></div>';
				} else {
					$saveSettingsObj["thank_you_double_opt_in_message"] = $_POST["thank_you_double_opt_in_message"];
				}

				$result = TenCentDao::saveContactListSettings($id, $saveSettingsObj);
				$listSettings = TenCentDao::getContactListSettings($id);

				echo '<div class="updated fade" id="successMessage"><p>Successfully saved completed contact list settings.</p></div>';

			}
		}
	}
	?>

	<ul class="navigation">
		<li class="active tencent-tab" data-tab="base-settings-tab">General</li>
		<li class="tencent-tab" data-tab="contact-list-settings-tab">Contact List</li>
		<li class="tencent-tab" data-tab="shortcode-settings-tab">Shortcode Generator</li>
	</ul>

	<div class="tabcontent-outerwrapper">

	<div id="shortcode-settings-tab" class="tencent-tabcontent">
		<div class="tab-innercontent">

			<div id="tencentmail-no-lists-wrapper" style="display:none" class="critical nolists">
				<h2 class="critical-setting">Open the 10CentMail Desktop App and Create a new Contact List</h2>

				<p>Contact Lists must be synced with the plugin to continue</p>
			</div>

			<div class="settings-wrapper containslists">
				<h2>Shortcode Generator</h2>

				<p>Select a list and check the field options below to create a shortcode</p>

				<div class="shortcode-generator">

					<h3>1. Select Contact List</h3>

					<div class="shortcode-listselect">
						<select id="shortcodeContactList">
							<option value="">- Select Contact List</option>
							<?php echo $contactListDropDownOptions ?>
						</select>
					</div>

					<h3>2. Short Code Options</h3>
					
					<span class="shortcode-option">
						<label>First Name : </label><input type="checkbox" id="firstName" class="shortcode-field"
						                                   data-field="first_name"/>
					</span>
					
					<span class="shortcode-option">
						<label>Last Name : </label><input type="checkbox" id="lastName" class="shortcode-field"
						                                  data-field="last_name"/>
					</span>
					
					<span class="shortcode-option">
						<label>Full Name : </label><input type="checkbox" id="fullName" class="shortcode-field"
						                                  data-field="full_name"/>
					</span>
					
					<span class="shortcode-option">
						<label>Requires Double Opt : </label><input type="checkbox" id="requiresDouble"
						                                            class="shortcode-field"
						                                            data-field="requires_double_opt"/>
					</span>

					<br/>
					<span class="shortcode-option">
						<div class="custom-field-left">
							<label for="customField">Add Custom Field : </label><input type="checkbox" id="customField"
							                                                           class="shortcode-field"
							                                                           data-field="custom_field"/>

							<input type="text" id="customLabel"/>
							<span id="customLabelError">Enter a custom label</span>
							<br class="clear"/>
						</div>
						<br class="clear"/>
					</span>

					<br/>
					<span class="shortcode-option">
						<div class="custom-field-left">
							<label for="redirectUrlField">Redirect To Url : </label><input type="checkbox"
							                                                               id="redirectUrlField"
							                                                               class="shortcode-field"
							                                                               data-field="redirect_url"/>

							<input type="text" id="redirectUrl"/>
							<span id="redirectUrlError">Enter a valid url</span>
							<br class="clear"/>
						</div>
						<br class="clear"/>
					</span>

					<h3>3. Copy Shortcode</h3>

					<div class="shortcode-output">
						<span class="note">Copy &amp; Paste into Post or Page to render Form</span>

						<p id="shortcode-output"></p>
					</div>

				</div>

			</div>

		</div>

	</div>


	<div id="base-settings-tab" class="tencent-tabcontent">

		<div class="tab-innercontent">

			<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

				<input type="hidden" name="settings-form" value="BASE_EMAIL_SETTINGS">

				<div class="settings-wrapper" id="base">

					<h2>General</h2>

					<ul>

						<li>
							<label for="tencentmail_key">
								10CentMail Key :
								<span class="note"><span class="critical-setting"> * Required</span> The 10CentMail Application will communicate with your Wordpress plugin via a secret key.  If you have not done so already, please create a secret Key now.  This key is used as a identification between 10CentMail and your website.  </span>
							</label>
							<input type="text" id="tencentmail_key" size="20" name="tencentmail_key"
							       value="<?php echo TenCentDao::getSetting("tencentmail_key"); ?>"
							       class="regular-text"/>
						</li>

						<li>
							<label for="tencentmail_company_name">Company Name : </label>
							<span class="note">Used in Confirmation and Thank you Emails</span>
							<input type="text" size="20" name="tencentmail_company_name"
							       value="<?php echo TenCentDao::getSetting("tencentmail_company_name"); ?>"
							       class="regular-text"/>
						</li>
						<li>
							<label for="tencentmail_support_email">
								Support Email Address :
								<span class="note">Email Address displayed within default Thank you Emails</span>
							</label>
							<input type="text" size="20" name="tencentmail_support_email"
							       value="<?php echo TenCentDao::getSetting("tencentmail_support_email"); ?>"
							       class="regular-text"/>
						</li>
						<li>
							<label for="tencentmail_from_email">
								From Email Address :
								<span class="note">The return email address displayed in thank you emails</span>
							</label>
							<input type="text" size="20" name="tencentmail_from_email"
							       value="<?php echo TenCentDao::getSetting("tencentmail_from_email"); ?>"
							       class="regular-text"/>
						</li>
						<li>
							<label for="tencentmail_from_name">
								From Email Name :
								<span class="note">The name displayed in thank you emails.  Typically the same as Company Name</span>
							</label>
							<input type="text" size="20" name="tencentmail_from_name"
							       value="<?php echo TenCentDao::getSetting("tencentmail_from_name"); ?>"
							       class="regular-text"/>
						</li>
						<li>
							<label for="tencentmail_notification_emails">
								Notification Email Addresses :
								<span class="note">Email Addresses used to when sending notifications for new subscriptions and confirmations. <br/>Comma Separate the list of emails</span>
							</label>
							<textarea
								name="tencentmail_notification_emails"><?php echo TenCentDao::getSetting("tencentmail_notification_emails"); ?></textarea>
						</li>
					</ul>

					<p class="submit">
						<input id="submit" class="button-primary" type="submit" value="Save Base Settings"
						       name="info_update"/>
					</p>

				</div>

			</form>

		</div>

	</div>


	<div id="contact-list-settings-tab" class="tencent-tabcontent">
	<div class="tab-innercontent">

	<div class="tencentmail-list-settings-wrapper" id="tencentmail-list-settings" style="display:none">


	<h2 class="contactLists">Contact List Settings for : <span class="critical"><?php echo $list ?></span></h2>

	<p>Select a Contact List below to update settings</p>

	<p>
		<select id="tencentmail-lists-select">
			<option value="">- select list</option>
			<?php echo $contactListDropDownOptions ?>
		</select>
	<p>

		<?php if ($inactive) { ?>

	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<div class="critical">
			<h3 class="critical-setting"
			    style="margin:5px auto; line-height:1.1em; padding:2px 0px;"><?php echo $list ?> is Inactive</h3>
			<input type="hidden" name="settings-form" value="DELETE_CONTACT_LIST"/>
			<input type="hidden" name="list" value="<?php echo $list ?>"/>
			<input type="hidden" name="id" value="<?php echo $id ?>"/>

			<p>The list selected is no longer active. Would you like to delete?</p>

			<p class="submit">
				<input id="submit" class="button-primary" type="submit"
				       value="Delete Contact List : <?php echo $list ?>" name="info_update"/>
			</p>
		</div>
	</form>
	<?php } ?>



	<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

	<input type="hidden" name="settings-form" value="CONTACT_LIST_SETTINGS"/>

	<!--
		************************
		Subscribe Form Settings
	   ************************
	-->
	<div class="settings-wrapper" id="subscribeform">

		<h3><?php echo $list ?> : Subscribe Form Settings</h3>

		<ul>
			<li>
				<label for="subscribe_button_text">Subscribe Button Text : </label>
				<span class="note">The text displayed in the Subscribe Form</span>
				<input type="text" size="20" name="subscribe_button_text"
				       value="<?php echo $listSettings['subscribe_button_text'] ?>" class="regular-text"/>
			</li>
			<li>
				<label for="subscribe_success_message">
					Subscribe Success Message :
					<span
						class="note">The message displayed immediately after the visitor successfully subscribes</span>
					<!-- <br/>Tags available : {{company_name}}, {{support_email}}</span> -->
				</label>
				<?php wp_editor(
					$listSettings['subscribe_success_message'],
					"subscribe_success_message",
					array(
						'media_buttons' => false,
						'textarea_name' => "subscribe_success_message",
						'textarea_rows' => 8
					));?>

			</li>
		</ul>

	</div>


	<!--
		*********************
		Unsubscribe Settings
	   *********************
	-->
	<div class="settings-wrapper" id="unsubscribeform">

		<h3><?php echo $list ?> : Unsubscribe Settings</h3>

		<ul>
			<li>
				<label for="unsubscribe_link_text">
					Unsubscribe Link Text :
					<span class="note">Both the Thank you for subscribing and Thank you for Opting-In emails include a link to unsubscribe.  </span>
				</label>
				<input type="text" size="20" name="unsubscribe_link_text"
				       value="<?php echo $listSettings['unsubscribe_link_text']; ?>" class="regular-text"/>
			</li>
			<li>
				<label for="unsubscribe_button_text">Unsubscribe Button Text : </label>
				<span class="note">The text displayed on the Unsubscribe Button</span>
				<input type="text" size="20" name="unsubscribe_button_text"
				       value="<?php echo $listSettings['unsubscribe_button_text']; ?>" class="regular-text"/>
			</li>
			<li>
				<label for="unsubscribe_page_content">
					Unsubscribe Page Content :
					<span class="note">The content displayed on the Unsubscribe Confirmation Page</span>
					<!-- <br/>Tags available : {{company_name}}, {{support_email}}</span> -->
				</label>
				<?php wp_editor(
					$listSettings['unsubscribe_page_content'],
					"unsubscribe_page_content",
					array(
						'media_buttons' => false,
						'textarea_name' => "unsubscribe_page_content",
						'textarea_rows' => 8
					));?>

			</li>
			<li>
				<label for="unsubscribe_success_message">
					Unsubscribe Success Page Content :
					<span class="note">The content displayed on after visitor successfully Unsubscribes</span>
					<!-- <br/>Tags available : {{company_name}}, {{support_email}}</span> -->
				</label>
				<?php wp_editor(
					$listSettings['unsubscribe_success_message'],
					"unsubscribe_success_message",
					array(
						'media_buttons' => false,
						'textarea_name' => "unsubscribe_success_message",
						'textarea_rows' => 8
					));?>

			</li>
		</ul>

	</div>


	<!--
		*****************************************
		Thank You for Subscribing Email Settings
	   *****************************************
	-->
	<div class="settings-wrapper" id="thankyousub">

		<h3><?php echo $list ?> : Thank You for Subscribing Email Settings</h3>

		<ul>
			<li>
				<label for="thank_you_subscribe_subject">Thank You for Subscribing Subject : </label>
				<span class="note">The subject of subscription thank you email</span>
				<input type="text" size="20" name="thank_you_subscribe_subject"
				       value="<?php echo $listSettings['thank_you_subscribe_subject']; ?>" class="regular-text"/>
			</li>
			<li>
				<label for="thank_you_subscribe_message">
					Thank You for Subscribing Message :
										<span class="note">Email message used when sending subscription Thank You emails.  
										<!-- <br/>Tags available : {{company_name}}, {{support_email}}</span> -->
				</label>
				<?php wp_editor(
					$listSettings['thank_you_subscribe_message'],
					"thank_you_subscribe_message",
					array(
						'media_buttons' => false,
						'textarea_name' => "thank_you_subscribe_message",
						'textarea_rows' => 8
					));?>

			</li>
		</ul>

	</div>


	<!--
		******************************************
		Double Opt-In Confirmation Email Settings
	   ******************************************
	-->
	<div class="settings-wrapper" id="doubleopt">

		<h3><?php echo $list ?> : Double Opt-In Confirmation Email Settings</h3>

		<ul>
			<li>
				<label for="double_opt_in_confirmation_subject">Double Opt-In Subject : </label>
				<span class="note">The subject of double opt in email</span>
				<input type="text" size="20" name="double_opt_in_confirmation_subject"
				       value="<?php echo $listSettings['double_opt_in_confirmation_subject']; ?>" class="regular-text"/>
			</li>
			<li>
				<label for="double_opt_in_confirmation_link_content">Double Opt-In Link : </label>
				<span class="note">The double Opt-In Confirmation Link Content </span>
				<input type="text" size="20" name="double_opt_in_confirmation_link_content"
				       value="<?php echo $listSettings['double_opt_in_confirmation_link_content']; ?>"
				       class="regular-text"/>
			</li>
		</ul>

	</div>


	<!--
		*******************************************
		Thank You for Double Opt-In Email Settings
	   *******************************************
	-->
	<div class="settings-wrapper" id="thankyoudouble">

		<h3><?php echo $list ?> : Thank You for Double Opt-In Email Settings</h3>

		<ul>
			<li>
				<label for="thank_you_double_opt_in_subject">Thank you Double Opt-In Subject : </label>
				<span class="note">The subject of double opt-in thank you email</span>
				<input type="text" size="20" name="thank_you_double_opt_in_subject"
				       value="<?php echo $listSettings['thank_you_double_opt_in_subject']; ?>" class="regular-text"/>
			</li>
			<li>
				<label for="thank_you_double_opt_in_message">
					Thank you Double Opt-In Message :
										<span class="note">Email message used when sending Opt-In Thank You emails.  
										<!-- <br/>Other tags available : {{company_name}}, {{support_email}}</span> -->
				</label>
				<?php wp_editor(
					$listSettings['thank_you_double_opt_in_message'],
					"thank_you_double_opt_in_message",
					array(
						'media_buttons' => false,
						'textarea_name' => "thank_you_double_opt_in_message",
						'textarea_rows' => 8
					));?>

			</li>
		</ul>

	</div>


	<p class="submit">
		<input id="submit" class="button-primary" type="submit" value="Save Settings for <?php echo $list ?>"
		       name="info_update"/>
	</p>

	</form>

	</div>


	<div id="tencentmail-no-lists-wrapper" style="display:none" class="critical nolists">
		<h2 class="critical-setting">Open the 10CentMail Desktop App and Create a new Contact List</h2>

		<p>Subscribe &amp; Unsubscribe Settings Cannot be administered without Contact Lists to associate them to.</p>

		<p>To create a new Contact List, open the 10CentMail Desktop App and navigate to the Contact Lists tab.

		<p>
	</div>


	<div id="tencentmail-incorrect-list" style="display:none" class="critical">
		<h2 class="critical-setting">List not found</h2>

		<p>The list name selected cannot be found. Try a different one.</p>

		<p>
			<select id="tencentmail-lists-select-two">
				<option value="">- select list</option>
				<?php echo $contactListDropDownOptions ?>
			</select>
		<p>
	</div>


	<div id="tencentmail-lists-available-non-selected" style="display:none" class="settings-wrapper">
		<h2 class="">Contact List Settings</h2>

		<p>Select a Contact List below to update settings</p>

		<p>
			<select id="tencentmail-lists-select-three">
				<option value="">- select list</option>
				<?php echo $contactListDropDownOptions ?>
			</select>
		<p>
	</div>


	</div>

	</div>

	</div>

	</div>

	<a href="<?php echo site_url() . '?10cent=debug&tcmKey=' . $key; ?>" target="_blank"
	   style="margin: 10px 20px; display:block; text-decoration:none; text-align:right; color:#989898; font-size:10px">debug</a>

	<div style="background: #EEF6E6; padding: 1em 2em; border: 1px solid #E4E4E4; margin-top:30px" class="wrap"
	     id="whatisten">
		<h2>What is 10CentMail?</h2>

		<p>10CentMail is Email Marketing Software for small business built on Amazon Simple Email Service.</p>

		<p>10CentMail manages your Email Campaigns, Email Lists, List Subscriptions, Email Reply’s, Blocked Emails (such
			as bounced, complaint, and blacklisted emails) and everything else you need to run a successful email
			marketing campaign.</p>

		<p><a href="http://10centmail.com" target="_blank" class="button-secondary">Visit 10CentMail</a></p>

	</div>


	</div>

	<script type="text/javascript">

		$ = jQuery;

		var baseUrl = '<?php echo get_bloginfo("url") ?>';
		var admin = '/wp-admin/options-general.php';
		var tencent_settings_page = '&page=tencentmail_settings';
		var contactLists = '<?php echo count($contactLists) ?>';
		var contactListId = '<?php echo $id ?>';

		$(document).ready(function () {

			$listSettingWrapper = $('#tencentmail-list-settings');
			$noListWrapper = $('#tencentmail-no-lists-wrapper');
			$incorrectContactList = $('#tencentmail-incorrect-list');
			$noListSelected = $('#tencentmail-lists-available-non-selected');

			$nolists = $('.nolists');
			$containslists = $('.containslists');

			$select = $('#tencentmail-lists-select');
			$selectTwo = $('#tencentmail-lists-select-two');
			$selectThree = $('#tencentmail-lists-select-three');

			$tabs = $('.tencent-tab');
			$tabcontent = $('.tencent-tabcontent');
			$tabinnercontent = $('.tab-innercontent');
			$tabouterwrapper = $('.tabcontent-outerwrapper');

			$shortcodeContactList = $('#shortcodeContactList');
			$shortcodeOutput = $('#shortcode-output');
			$shortcodeCheckbox = $('.shortcode-field');
			$shortcodeOptions = $('.shortcode-option');

			$customLabelInput = $('#customLabel');
			$customCheckbox = $('#customField');
			$customLabelError = $('#customLabelError');

			$redirectUrlInput = $('#redirectUrl');
			$redirectUrlCheckbox = $('#redirectUrlField');
			$redirectUrlError = $('#redirectUrlError');

			$select.change(getListSettings($select));
			$selectTwo.change(getListSettings($selectTwo));
			$selectThree.change(getListSettings($selectThree));

			$shortcodeContactList.change(updateShortcode($shortcodeContactList));
			$shortcodeOptions.click(updateShortcode($shortcodeContactList));
			$customLabelInput.blur(updateShortcode($shortcodeContactList));
			$redirectUrlInput.blur(updateShortcode($shortcodeContactList));

			$tabs.click(switchtab);

			if (contactLists > 0) {
				if (contactListId == "") {
					$nolists.hide();
					$containslists.show();
					$listSettingWrapper.hide();
					$noListSelected.show();
				} else {
					$noListSelected.hide();
					$nolists.hide();
					$containslists.show();
					$listSettingWrapper.show();
				}
			} else if (contactLists == 0 && contactListId == "") {
				$nolists.show();
				$containslists.hide();
			} else {
				$incorrectContactList.show();
			}

			function updateShortcode($selectBoxUsed) {
				return function (event) {
					$shortcodeOutput.html("");
					var contactList = $selectBoxUsed.val();
					var shortcode = "[tencentmail_subscribe_form ";
					if (contactList) {
						shortcode += 'list="' + contactList + '" ';
						shortcode += getCheckedOptions();
						shortcode += "]";
						$shortcodeOutput.html(shortcode);
					} else {
						//alert('select a contact list');
					}
					checkCustomCheckbox($customCheckbox, $customLabelInput, $customLabelError);
					checkCustomCheckbox($redirectUrlCheckbox, $redirectUrlInput, $redirectUrlError);
				}
			}

			function getCheckedOptions() {
				var fields = "";
				$shortcodeCheckbox.each(function (index, checkbox) {
					var $checkbox = $(checkbox);
					if ($checkbox.attr('checked')) {
						if ($checkbox.attr('data-field') == 'custom_field') {
							fields += 'custom_label="' + $customLabelInput.val() + '" ';
						} else if ($checkbox.attr('data-field') == 'redirect_url') {
							fields += 'redirect_url="' + $redirectUrlInput.val() + '" ';
						} else {
							fields += $checkbox.attr('data-field') + '="true" ';
						}
					}
				});
				return fields;
			}

			function checkCustomCheckbox(checkboxControl, inputControl, errorControl) {
				if (checkboxControl.attr('checked')) {
					inputControl.animate({"opacity": 1}, 50, $.noop);
					if (inputControl.val() == "") {
						errorControl.animate({"opacity": 1}, 50, $.noop);
					} else {
						errorControl.animate({"opacity": 0}, 50, $.noop);
					}
				} else {
					inputControl.animate({"opacity": 0}, 50, $.noop);
					errorControl.animate({"opacity": 0}, 50, $.noop);
				}
			}

			function getListSettings($selectBoxUsed) {
				return function (event) {
					list = '?list=' + $selectBoxUsed.val();
					refreshWithListURL = constructUrl();
					window.location = refreshWithListURL;
				}
			}

			$tabcontent.hide();

			$tabs.removeClass('active');

			if (contactListId == "") {

				var tab = $($tabs[0]).attr('data-tab');
				$tab = $('#' + tab);
				$tab.show()
					.find('.tab-innercontent')
					.show();
				$($tabs[0]).addClass('active');

			} else {
				var tab = $($tabs[1]).attr('data-tab');
				$tabs[1].click();
			}

			function switchtab(event) {

				var $target = $(event.target);
				$tabs.removeClass('active');
				$target.addClass('active');

				var tab = $target.attr('data-tab');

				$tab = $('#' + tab);

				$tabcontent.hide();
				$tabinnercontent.hide();

				$tab.show()
					.find('.tab-innercontent')
					.show(0, function () {
						var height = $tab.height() + "px";
						//console.log(height);
						$tabouterwrapper.css({
							"height": height
						});
					});
			}
		});

		function constructUrl() {
			return baseUrl + admin + list + tencent_settings_page + '#tencentmail-list-settings';
		}

	</script>
<?php
}

?>