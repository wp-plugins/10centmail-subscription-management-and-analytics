<?php
/*
Plugin Name: 10CentMail
Plugin URI: http://10centmail.com/blog/10centmail-wordpress-plugin/
Description: 10CentMail Subscription Management and Analytics plugin for Wordpress.
Version: 2.0.5
Author: 10CentMail
Author URI: http://10centmail.com
License: GPL
*/

include_once('util/Utils.php');
include_once('util/TenDaoUtil.php');
include_once('lib/Mustache.php');
include_once('lib/Logging.php');
include_once('TenCentDao.php');
include_once('TenCentAdmin.php');
include_once('TenCentForm.php');
include_once('TenCentRequest.php');
include_once('TenCentEmailer.php');
include_once('validation/DataValidator.php');
include_once('resources/mappings/SubscriptionMapper.php');
include_once('resources/templates/TenCentCss.php');
include_once('resources/templates/TenCentSubscribeTmpl.php');
include_once('validation/validators/IValidator.php');
include_once('validation/validators/impl/EmailValidator.php');
include_once('validation/validators/impl/IPAddressValidator.php');
include_once('validation/validators/impl/NoSpecialCharactersValidator.php');
include_once('validation/validators/impl/NotEmptyValidator.php');
include_once('validation/validators/impl/StringLengthValidator.php');

include_once('actions/aws_sns.php');
include_once('actions/confirm_double_opt.php');
include_once('actions/contact_lists.php');
include_once('actions/data.php');
include_once('actions/endpoints.php');
include_once('actions/metadata.php');
include_once('actions/track.php');
include_once('actions/unsubscribe.php');

add_action('wp_head', 'do_shortcode');
add_action('admin_menu', 'tcm_add_settings');
add_action('wp_head', 'tencentmail_endpoints_metadata');
add_action('wp_head', 'tencentmail_version_metadata');
add_action('init', 'do_output_buffer'); //allow redirection, even if my theme starts to send output to the browser
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tcm_plugin_actions', 10, 1);
register_activation_hook(__FILE__, 'tencentmail_install');
register_deactivation_hook(__FILE__, 'tencentmail_uninstall');


function tencentmail_install()
{
	try {
		$pluginData = get_plugin_data(__FILE__);

		if (!TenDaoUtil::tableExists(TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE)) ||
			!TenCentDao::settingExists("tencentmail_version")
		) {

			//create tables
			TenCentDao::createTables();

			//save settings
			TenCentDao::addSetting("tencentmail_key", "");
			TenCentDao::addSetting("tencentmail_version", $pluginData["Version"]);
//			TenCentDao::addSetting("tencentmail_wordpress_endpoints_url", get_endpoints_url());
			TenCentDao::addSetting("tencentmail_company_name", get_bloginfo('name'));
			TenCentDao::addSetting("tencentmail_from_email", get_bloginfo('admin_email'));
			TenCentDao::addSetting("tencentmail_from_name", get_bloginfo('name'));
			TenCentDao::addSetting("tencentmail_support_email", get_bloginfo('admin_email'));
			TenCentDao::addSetting("tencentmail_notification_emails", get_bloginfo('admin_email'));
		}

		TenCentDao::addSetting("tencentmail_version", $pluginData["Version"]);
	} catch (Exception $e) {
		trigger_error($e->getMessage(), E_USER_ERROR);
	}
}

function tencentmail_uninstall()
{
	remove_action('wp_head', 'do_shortcode');
	remove_action('admin_menu', 'tcm_add_settings');
	remove_action('wp_head', 'tencentmail_endpoints_metadata', 0);
	remove_action('wp_head', 'tencentmail_version_metadata', 0);
	remove_shortcode('tencentmail_subscribe_form');
}


function get_endpoints_url()
{
	return get_option('siteurl') . '/?10cent=endpoints';
}


function tcm_add_settings()
{
	add_options_page("10CentMail Plugin Settings", "10CentMail", 'manage_options', 'tencentmail_settings', 'tcm_settings_page');

	$icon = str_replace("TenCent.php", "resources/images/tencentmail-icon.png", plugin_basename(__FILE__));
	$iconurl = plugins_url() . '/' . $icon;

	add_menu_page("10CentMail Plugin Settings", "10CentMail", 'manage_options', 'tencentmail_settings', 'tcm_settings_page', $iconurl);
}


function tcm_plugin_actions($links)
{
	$settings_link = '<a href="' . admin_url('options-general.php?page=tencentmail_settings') . '">' . __('Settings') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
}

function do_output_buffer()
{
	ob_start();
}

add_shortcode('tencentmail_subscribe_form', 'TenCentForm::renderSubscribeForm');
