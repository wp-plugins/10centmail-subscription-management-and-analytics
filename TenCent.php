<?php
defined('ABSPATH') OR exit;
/*
Plugin Name: 10CentMail
Plugin URI: http://10centmail.com/blog/10centmail-wordpress-plugin/
Description: 10CentMail Subscription Management and Analytics plugin for Wordpress.
Version: 2.1.36
Author: 10CentMail
Author URI: http://10centmail.com
License: GPL
*/

//global $wpdb;
//$wpdb->show_errors(true);

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

// this is where we ensure that the databases are available
add_action('plugins_loaded', 'tencentmail_setup_database');

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tcm_plugin_actions', 10, 1);
register_activation_hook(__FILE__, 'tencentmail_activate');
register_deactivation_hook(__FILE__, 'tencentmail_deactivate');
register_uninstall_hook(__FILE__, 'tencentmail_uninstall');

function pluginData()
{
	$default_headers = array(
		'Name' => 'Plugin Name',
		'PluginURI' => 'Plugin URI',
		'Version' => 'Version',
		'Description' => 'Description',
		'Author' => 'Author',
		'AuthorURI' => 'Author URI',
		'TextDomain' => 'Text Domain',
		'DomainPath' => 'Domain Path',
		'Network' => 'Network'
	);

	$plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');
	return $plugin_data;
}

function tencentmail_activate()
{
	if (!current_user_can('activate_plugins'))
		return;
	//tencentmail_setup_database();
}

function tencentmail_deactivate()
{
	if (!current_user_can('activate_plugins'))
		return;
	remove_action('wp_head', 'do_shortcode');
	remove_action('admin_menu', 'tcm_add_settings');
	remove_action('wp_head', 'tencentmail_endpoints_metadata', 0);
	remove_action('wp_head', 'tencentmail_version_metadata', 0);
	remove_shortcode('tencentmail_subscribe_form');
}

function tencentmail_uninstall()
{
	TenCentDao::dropTables();
}

/**
 * creates tcm tables if they do not exist and inserts default data. this method can and will be run several times
 * so it is important that it be as lightweight as possible even though it is doing heavy lifting.
 */
function tencentmail_setup_database()
{
	try {
		$pluginData = pluginData();

		if (!TenDaoUtil::tableExists(TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE)) ||
			!TenCentDao::settingExists("tencentmail_version")
		) {

			//create tables
			TenCentDao::createTables();

			//save settings
			TenCentDao::addSetting("tencentmail_key", "");
			TenCentDao::addSetting("tencentmail_version", $pluginData["Version"]);
			TenCentDao::addSetting("tencentmail_company_name", get_bloginfo('name'));
			TenCentDao::addSetting("tencentmail_from_email", get_bloginfo('admin_email'));
			TenCentDao::addSetting("tencentmail_from_name", get_bloginfo('name'));
			TenCentDao::addSetting("tencentmail_support_email", get_bloginfo('admin_email'));
			TenCentDao::addSetting("tencentmail_notification_emails", get_bloginfo('admin_email'));
		} else {
//			TenCentDao::addSiteIdToExistingTables();
		}

		TenCentDao::addSetting("tencentmail_version", $pluginData["Version"]);
	} catch (Exception $e) {
		trigger_error($e->getMessage(), E_USER_ERROR);
	}
}

function get_endpoints_url()
{
	return get_option('siteurl') . '/?10cent=endpoints';
}


function tcm_add_settings()
{
	tencentmail_setup_database();

	if (current_user_can('manage_options')) {
		add_options_page("10CentMail Plugin Settings", "10CentMail", 'manage_options', 'tencentmail_settings', 'tcm_settings_page');
	}

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


//class TenCentPlugin
//{
//	public static function ensure_tables_exist()
//	{
//		tencentmail_activate();
//	}
//
//	public static function uninstall()
//	{
//		tencentmail_deactivate();
//	}
//}
