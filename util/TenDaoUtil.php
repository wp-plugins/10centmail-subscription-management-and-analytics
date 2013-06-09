<?php

class TenDaoUtil
{
	const SUBSCRIBED = 1;
	const UNSUBSCRIBED = 0;

	const SUBSCRIBE_TABLE = "tencent_subscribe";
	const TRACKING_TABLE = "tencent_tracking";
	const SETTINGS_TABLE = "tencent_settings";
	const CONTACT_LISTS_TABLE = "tencent_contact_lists";
	const CONTACT_LIST_SETTINGS_TABLE = "tencent_contact_list_settings";
	const SNS_MESSAGE_TABLE = "tencent_sns_message";

	const SUBSCRIBERS = "subscribers";
	const UNSUBSCRIBERS = "unsubscrbers";
	const TRACK_DATA = "track";
	const ALL_DATA = "all";

	const NOTIFICATION_PARAM = "sns-notifications";
	const SUBSCRIPTION_CONFIRM_PARAM = "sns-subscription-confirmations";
	const NOTIFICATION_STRING = "Notification";
	const NOTIFICATION = 0;

	const SUBSCRIPTION_CONFIRM_STRING = "SubscriptionConfirmation";
	const SUBSCRIPTION_CONFIRM = 1;

	const MYSQL_QUOTE_CHARACTER = "`";

	public static function tableExists($tableName)
	{
		global $wpdb;
		//$tn = trim($tableName, self::MYSQL_QUOTE_CHARACTER);
		$tn = $tableName;
		$tables = $wpdb->get_results("show tables like '$tn'");
		return (sizeof($tables) == 0) ? false : true;
	}

	public static function getAppTableNames()
	{
		$result = array(
			self::SUBSCRIBE_TABLE,
			self::TRACKING_TABLE,
			self::SETTINGS_TABLE,
			self::CONTACT_LISTS_TABLE,
			self::CONTACT_LIST_SETTINGS_TABLE,
			self::SNS_MESSAGE_TABLE
		);

		return $result;
	}

	public static function getInstalledTableNames()
	{
		global $wpdb;
		$result = array();
		$tableNames = self::getAppTableNames();
		foreach($tableNames as $tableName){
			$tables = $wpdb->get_results("show tables like '%$tableName%'");
			array_push($result, $tables);
		}
		return $result;
	}

	public static function getTableName($tableName)
	{
//		global $wpdb;
//		return $wpdb->base_prefix . $tableName;
//		return $wpdb->prefix . $tableName;
		return self::getMySqlTableName($tableName);
	}

	private static function getMySqlTableName($tableName)
	{
		global $wpdb;
		//$result = $wpdb->base_prefix . $tableName;
		$result = $wpdb->get_blog_prefix() . $tableName;
		//return self::MYSQL_QUOTE_CHARACTER . $result . self::MYSQL_QUOTE_CHARACTER;
		return $result;
	}

	public static function getSubscriptionTableSQL()
	{
		$tableName = self::getTableName(self::SUBSCRIBE_TABLE);
//		return "CREATE TABLE $tableName (
//		  	id mediumint(9) NOT NULL AUTO_INCREMENT,
//			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
//			email VARCHAR(255) NOT NULL,
//			list VARCHAR(255) NOT NULL,
//			firstName VARCHAR(255),
//			lastName VARCHAR(255),
//			fullName VARCHAR(255),
//			customField VARCHAR(255),
//			ip VARCHAR(255),
//			status int NOT NULL,
//			campaignId int,
//			confirmedDoubleOpt int NULL,
//			requiresDoubleOpt int NOT NULL,
//			siteId int(11),
//			UNIQUE KEY id (id)
//		);";
		return "CREATE TABLE $tableName (
		  	id mediumint(9) NOT NULL AUTO_INCREMENT,
			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			email VARCHAR(255) NOT NULL,
			list VARCHAR(255) NOT NULL,
			firstName VARCHAR(255),
			lastName VARCHAR(255),
			fullName VARCHAR(255),
			customField VARCHAR(255),
			ip VARCHAR(255),
			status int NOT NULL,
			campaignId int,
			confirmedDoubleOpt int NULL,
			requiresDoubleOpt int NOT NULL,
			UNIQUE KEY id (id)
		);";
	}

	public static function getTrackingTableSQL()
	{
		$tableName = self::getTableName(self::TRACKING_TABLE);
//		return "CREATE TABLE $tableName (
//			id mediumint(9) NOT NULL AUTO_INCREMENT,
//			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
//			trackingId VARCHAR(255) NOT NULL,
//			type VARCHAR(255) NOT NULL,
//			ip VARCHAR(255),
//			agent VARCHAR(255),
//			url VARCHAR(255),
//			referer VARCHAR(255),
//			siteId int(11),
//			UNIQUE KEY id (id)
//		);";
		return "CREATE TABLE $tableName (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			trackingId VARCHAR(255) NOT NULL,
			type VARCHAR(255) NOT NULL,
			ip VARCHAR(255),
			agent VARCHAR(255),
			url VARCHAR(255),
			referer VARCHAR(255),
			UNIQUE KEY id (id)
		);";
	}

	public static function getListsTableSQL()
	{
		$tableName = self::getTableName(self::CONTACT_LISTS_TABLE);
//		return "CREATE TABLE $tableName (
//			id mediumint(9) NOT NULL AUTO_INCREMENT,
//			list VARCHAR(255) NOT NULL,
//			active int NOT NULL,
//			siteId int(11),
//			UNIQUE KEY id (id)
//		);";
		return "CREATE TABLE $tableName (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			list VARCHAR(255) NOT NULL,
			active int NOT NULL,
			UNIQUE KEY id (id)
		);";
	}

	public static function getSettingsTableSQL()
	{
		$tableName = self::getTableName(self::SETTINGS_TABLE);
//		return "CREATE TABLE $tableName (
//			id mediumint(9) NOT NULL AUTO_INCREMENT,
//			setting VARCHAR(255) NOT NULL,
//			value TEXT NOT NULL,
//			siteId int(11),
//			UNIQUE KEY id (id)
//		);";
		return "CREATE TABLE $tableName (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			setting VARCHAR(255) NOT NULL,
			value TEXT NOT NULL,
			UNIQUE KEY id (id)
		);";
	}

	public static function getContactListSettingsTableSQL()
	{
		$tableName = self::getTableName(self::CONTACT_LIST_SETTINGS_TABLE);
//		return "CREATE TABLE $tableName (
//			id mediumint(9) NOT NULL AUTO_INCREMENT,
//			listId int NOT NULL,
//			unsubscribe_link_text TEXT,
//			unsubscribe_button_text TEXT,
//			unsubscribe_page_content TEXT,
//			unsubscribe_success_message TEXT,
//			subscribe_button_text TEXT,
//			subscribe_success_message TEXT,
//			thank_you_subscribe_subject TEXT,
//			thank_you_subscribe_message TEXT,
//			double_opt_in_confirmation_subject TEXT,
//			double_opt_in_confirmation_link_content TEXT,
//			thank_you_double_opt_in_subject TEXT,
//			thank_you_double_opt_in_message TEXT,
//			siteId int(11),
//			UNIQUE KEY id (id)
//		);";
		return "CREATE TABLE $tableName (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			listId int NOT NULL,
			unsubscribe_link_text TEXT,
			unsubscribe_button_text TEXT,
			unsubscribe_page_content TEXT,
			unsubscribe_success_message TEXT,
			subscribe_button_text TEXT,
			subscribe_success_message TEXT,
			thank_you_subscribe_subject TEXT,
			thank_you_subscribe_message TEXT,
			double_opt_in_confirmation_subject TEXT,
			double_opt_in_confirmation_link_content TEXT,
			thank_you_double_opt_in_subject TEXT,
			thank_you_double_opt_in_message TEXT,
			UNIQUE KEY id (id)
		);";
	}

	public static function getSnsMessageTableSQL()
	{
		$tableName = self::getTableName(self::SNS_MESSAGE_TABLE);
//		return "CREATE TABLE $tableName (
//			id mediumint(9) NOT NULL AUTO_INCREMENT,
//			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
//			type mediumint(9) NOT NULL,
//			message TEXT NOT NULL,
//			siteId int(11),
//			UNIQUE KEY id (id)
//		);";
		return "CREATE TABLE $tableName (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			type mediumint(9) NOT NULL,
			message TEXT NOT NULL,
			UNIQUE KEY id (id)
		);";
	}
}
