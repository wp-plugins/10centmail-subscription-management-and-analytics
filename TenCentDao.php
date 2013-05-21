<?php

class TenCentDao
{

	private static function getSiteId($isNetworkWide = false)
	{
		if ($isNetworkWide)
			return 0;
		if (is_multisite()) {
			return get_current_blog_id();
		} else {
			return 1;
		}
	}

	public static function saveSnsMessage($type, $message)
	{
		global $wpdb;
		$table = TenDaoUtil::getTableName(TenDaoUtil::SNS_MESSAGE_TABLE);
		$type = self::getMessageType($type);
		$data = array(
			"date" => date('Y-m-d H:i:s'),
			"type" => $type,
			"message" => "$message",
			"siteId" => TenCentDao::getSiteId()
		);

		$types = array("%s", "%s", "%d");
		$result = $wpdb->insert($table, $data, $types);

		return $result;
	}

	private static function getMessageType($type)
	{
		if ($type == TenDaoUtil::NOTIFICATION_STRING) {
			return TenDaoUtil::NOTIFICATION;
		}
		return TenDaoUtil::SUBSCRIPTION_CONFIRM;
	}

	public static function saveTrackingId($trackingId, $type, $url, $ip, $agent, $referer)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE);

		$data = array(
			"trackingId" => "$trackingId",
			"type" => "$type",
			"url" => "$url",
			"ip" => "$ip",
			"agent" => "$agent",
			"referer" => "$referer",
			"date" => date('Y-m-d H:i:s'),
			"siteId" => TenCentDao::getSiteId()
		);
		$types = array("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%d");
		$result = $wpdb->insert($tableName, $data, $types);

		return $result;
	}

	public static function updateContactLists($currentContactLists)
	{
		global $wpdb;
		$insertResult = 0;
		$updateResult = 0;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);

		$dbLists = self::getAllContactLists();
		$dbListsObj = self::getContactListsObj($dbLists);

		$currentListsArray = explode(",", $currentContactLists);
		$currentListsObj = Utils::toObjNoValue($currentListsArray);

		$inactiveLists = array();
		$activeLists = array();


		foreach ($currentListsArray as $contactList) {
			$list = trim($contactList);
			if ($dbListsObj->$list) {
				if ($dbListsObj->$list->active == Utils::FALSE) {
					$dbListsObj->$list->newStatus = Utils::TRUE;
				} else {
					unset($dbListsObj->$list);
				}
			} else {
				$dbListsObj->$list = (object)array();
				$dbListsObj->$list->list = $contactList;
				$dbListsObj->$list->active = Utils::TRUE;
				$dbListsObj->$list->update = false;
				$dbListsObj->$list->add = true;
			}
		}


		$separator = "";
		$count = 0;
		$insertSqlRaw = "INSERT INTO $tableName (list, active, siteId) VALUES ";
		foreach ($dbListsObj as $key => $contactList) {
			if (!$contactList->update && $contactList->add) {
				$count++;
				$insertSqlRaw .= $separator . "('" . trim($contactList->list) . "', " . Utils::TRUE . ", " . TenCentDao::getSiteId() . ")";
				$separator = ",";
			} else {
				array_push($inactiveLists, $contactList);
			}
		}


		foreach ($inactiveLists as $contactList) {
			$status = ($contactList->newStatus == Utils::TRUE ? Utils::TRUE : Utils::FALSE);
			$rawSql = "UPDATE $tableName set active=" . $status . " where id=$contactList->id and siteId=$contactList->siteId;";
			$updateSQL = $wpdb->prepare($rawSql);
			$updateResult = $wpdb->query($updateSQL);
		}


		if ($count > 0) {
			$insertSql = $wpdb->prepare($insertSqlRaw);
			$insertResult = $wpdb->query($insertSql);
		}

	}

	public static function getAllContactLists()
	{
		return self::getContactLists("WHERE siteId = " . TenCentDao::getSiteId());
	}

	public static function getActiveContactLists()
	{
		return self::getContactLists("WHERE active = 0 and siteId = " . TenCentDao::getSiteId());
	}

	public static function getInActiveContactLists()
	{
		return self::getContactLists("WHERE active = 1 and siteId = " . TenCentDao::getSiteId());
	}

	public static function getContactLists($whereClause)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
		$sql = "SELECT * FROM $tableName $whereClause ORDER BY active DESC;";
		$listsRows = $wpdb->get_results($sql);
		return $listsRows;
	}

	public static function getContactListsObj($dbLists)
	{
		$obj = (object)array();
		foreach ($dbLists as $id => $contactList) {
			$list = $contactList->list;
			$obj->$list = $contactList;
			$obj->$list->update = true;
		}
		return $obj;
	}

	public static function getContactListByList($list)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
		$siteId = TenCentDao::getSiteId();
		$sql = "SELECT * FROM $tableName WHERE list='$list' AND siteId = $siteId;";
		$listRow = $wpdb->get_row($sql);
		return $listRow;
	}

	public static function removeEverythingForContactList($id)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
		if (self::contactListExists($id)) {
			self::deleteContactList($id);
			self::deleteContactListSettings($id);
		}
	}

	public static function contactListExists($id)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
		$contactListRow = $wpdb->get_row("SELECT * FROM $tableName WHERE id = '$id';");
		return (sizeof($contactListRow) == 0) ? false : true;
	}

	public static function deleteContactListSettings($listId)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
		$siteId = TenCentDao::getSiteId();
		$deleteSql = "DELETE FROM `$tableName` WHERE listId = $listId AND siteId = $siteId;";
		return $wpdb->query($deleteSql);
	}

	public static function deleteContactList($id)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
		if (self::contactListExists($id)) {
			$siteId = TenCentDao::getSiteId();
			$deleteSql = "DELETE FROM `$tableName` WHERE id='$id' AND siteId = $siteId;";
			return $wpdb->query($deleteSql);
		}
	}

	public static function contactListExistsByList($list)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
		$siteId = TenCentDao::getSiteId();
		$contactListRow = $wpdb->get_row("SELECT * FROM $tableName WHERE list='$list' AND siteId = $siteId;");
		return (sizeof($contactListRow) == 0) ? false : true;
	}


	/*
	   ****************************
		Contact List Settings Logic
	   ****************************
	*/

	public static function getContactListSettings($listId)
	{
		global $wpdb;
		$table = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
		$query = "SELECT * FROM `$table` WHERE listId = $listId and siteId = " . TenCentDao::getSiteId();

		$settings = $wpdb->get_row($query, ARRAY_A);

		if (!empty($settings)) return $settings;

	}

	public static function saveContactListSettings($listId, $settings)
	{
		if (self::settingsExist($listId)) {
			self::updateContactListSettings($listId, $settings);
		} else {
			try {

				global $wpdb;
				$settings["listId"] = $listId;
				$dataTypes = self::getSettingsDataTypes($settings);

				$table = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
				$result = $wpdb->insert($table, $settings, $dataTypes);

			} catch (Exception $e) {
			}
		}
	}

	public static function settingsExist($listId)
	{
		global $wpdb;
		$table = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
		$query = "SELECT * FROM `$table` WHERE listId = '$listId' and siteId = " . TenCentDao::getSiteId();
		$results = $wpdb->query($query);
		return ($results > 0) ? true : false;
	}

	public static function updateContactListSettings($listId, $settings)
	{
		global $wpdb;

		$table = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
		$dataTypes = self::getSettingsDataTypes($settings);
		$settingsId = self::getSettingsId($listId);
		$where = array(
			"ID" => $settingsId,
			"siteId" => TenCentDao::getSiteId()
		);

		$result = $wpdb->update($table, $settings, $where);
		return $result;
	}

	public static function getSettingsDataTypes($settings)
	{
		$obj = array();
		foreach ($settings as $property => $value) {
			$obj[] = '%s';
		}

		return $obj;
	}

	public static function getSettingsId($listId)
	{
		global $wpdb;
		$table = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
		$siteId = TenCentDao::getSiteId();
		$row = $wpdb->get_row("SELECT * FROM $table WHERE listId = '$listId' AND siteId = $siteId", ARRAY_A);
		return $row['id'];
	}

	public static function confirmOptIn($email, $list)
	{
		global $wpdb;
		if (self::isSubscribed($email, $list)) {

			$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
			$data = array("confirmedDoubleOpt" => Utils::TRUE);

			$where = array(
				"ID" => self::getSubscriptionRowId($email, $list),
				"siteId" => TenCentDao::getSiteId()
			);
			$format = array(
				"%d",
				"%d"
			);

			$result = $wpdb->update($tableName, $data, $where, $format);

			if ($result == 1) return true;
			throw new Exception("Something went when trying to confirm Email address " . $email . " with list " . $list . " was not found");

		} else {
			throw new Exception("Email address " . $email . " with list " . $list . " was not found");
		}
	}

	public static function isSubscribed($email, $list)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		$sql = "SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::SUBSCRIBED . " and email = '$email' and list = '$list' and siteId = " . TenCentDao::getSiteId();
		$rows = $wpdb->get_row($sql);
		return (sizeof($rows) == 0) ? false : true;
	}

	public static function isUnSubscribed($email, $list)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		$sql = "SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::UNSUBSCRIBED . " and email = '$email' and list = '$list' and siteId = " . TenCentDao::getSiteId();
		$rows = $wpdb->get_row($sql);
		return (sizeof($rows) == 0) ? false : true;
	}

	public static function getInsertData($config)
	{
		$obj = array();
		foreach ($config as $property => $value) {
			if ($property != 'request_uri' && $property != 'redirect_url') {
				$obj[$config->$property->data_column] = $config->$property->value;
				if ($property == "requires_double_opt") {
					$obj[$config->$property->data_column] = ($config->$property->value == "true" ? Utils::TRUE : Utils::FALSE);
				}
			}
		}
		$obj["siteId"] = TenCentDao::getSiteId();
		return $obj;
	}

	public static function getDataTypes($config)
	{
		$obj = array();
		foreach ($config as $property => $value) {
			if ($property != 'request_uri' && $property != 'redirect_url') {
				$obj[] = $config->$property->placeholder;
			}
		}
		$obj["siteId"] = "%d";
		return $obj;
	}

	public static function getSubscriptionRowId($email, $list)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		$siteId = TenCentDao::getSiteId();
		$row = $wpdb->get_row("SELECT * FROM $tableName WHERE email = '$email' AND list = '$list' AND siteId = $siteId", ARRAY_A);
		return $row['id'];
	}

	public static function saveSubscriber($config)
	{

		global $wpdb;
		if (!self::isSubscribed($config->email->value, $config->list->value)) {

			$data = self::getInsertData($config);
			$dataTypes = self::getDataTypes($config);
			$result = $wpdb->insert(TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE), $data, $dataTypes);
			if ($result == 1) return true;
			throw new Exception("Something went wrong, please try again");

		} else {

			$table = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);

			$subscription = $wpdb->get_row("SELECT * FROM " . $table . " WHERE email='" . $config->email->value . "' AND list='" . $config->list->value . "' and siteId = " . TenCentDao::getSiteId() . ";");

			$rowId = $subscription->id;

			$data = self::getInsertData($config);
			$dataTypes = self::getDataTypes($config);

			$result = $wpdb->update(
				$table,
				$data,
				array(
					"ID" => $rowId
				),
				$dataTypes
			);
			if ($result == 1) return true;
			throw new Exception("Something went wrong, please try again");

		}

	}

	public static function tableExistsForType($type)
	{
		return (self::typeTableMapping()->$type != "" && self::typeTableMapping()->$type != null) ? true : false;
	}

	public static function typeTableMapping()
	{
		return (object)array(
			"subscribers" => TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE),
			"unsubscribers" => TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE),
			"track" => TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE)
		);
	}

	public static function dataRowExists($table, $rowId)
	{
		global $wpdb;
		$query = "SELECT * FROM `$table` WHERE id=$rowId";
		$results = $wpdb->query($query);

		return ($results > 0) ? true : false;
	}

	public static function getStatusClause($type)
	{
		if ($type == "subscribers") return " AND status=" . TenDaoUtil::SUBSCRIBED;
		if ($type == "unsubscribers") return " AND status=" . TenDaoUtil::UNSUBSCRIBED;
		return "";
	}

	public static function emailNotAlreadySubscribed($email, $list)
	{

		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		$results = $wpdb->query("SELECT * FROM `$tableName` WHERE status = " . TenDaoUtil::SUBSCRIBED . " and email = '$email' and list = '$list' and siteId = " . TenCentDao::getSiteId());

		return ($results == 0) ? true : false;
	}

	public static function getSubscriptions($list)
	{
		global $wpdb;
		$results = 0;
		if (isset($list)) {
			$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
			$querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::SUBSCRIBED . " and list = '$list' and siteId = " . TenCentDao::getSiteId(), ARRAY_A);
			$results = $wpdb->get_results($querySQL);
		}
		return $results;
	}

	public static function getUnSubscribers($list)
	{
		global $wpdb;
		$results = 0;
		if (isset($list)) {
			$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
			$querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status = " . TenDaoUtil::UNSUBSCRIBED . " and list = '$list' and siteId = " . TenCentDao::getSiteId(), ARRAY_A);
			$results = $wpdb->get_results($querySQL);
		}
		return $results;
	}

	public static function unsubscribe($email, $list, $campaignId)
	{
		global $wpdb;

		$now = date('Y-m-d H:i:s');
		$table = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		if (self::isSubscribed($email, $list) || self::isUnSubscribed($email, $list)) {
			$sql = "UPDATE `$table` SET status=" . TenDaoUtil::UNSUBSCRIBED . ", campaignId = $campaignId, `date` = '$now' WHERE email = '$email' and list = '$list' and siteId = " . TenCentDao::getSiteId();
			$stmt = $wpdb->prepare($sql);
			$result = $wpdb->query($stmt);
		} else {
			$data = array(
				"email" => $email,
				"list" => $list,
				"campaignId" => $campaignId,
				"date" => $now,
				"status" => TenDaoUtil::UNSUBSCRIBED,
				"requiresDoubleOpt" => Utils::FALSE,
				"siteId" => TenCentDao::getSiteId()
			);

			$types = array("%s", "%s", "%d", "%s", "%d", "%d", "%d");
			$result = $wpdb->insert($table, $data, $types);
		}

		try {
			return $result;
		} catch (Exception $e) {
			throw new Exception("something went wrong performing save");
		}
	}

	public static function addSetting($setting, $value, $isNetworkWide = false)
	{
		global $wpdb;
		if (!self::settingExists($setting, $isNetworkWide)) {

			$data = array(
				"setting" => $setting,
				"value" => $value,
				"siteId" => TenCentDao::getSiteId($isNetworkWide)
			);
			$format = array(
				"%s",
				"%s",
				"%d"
			);
			return $wpdb->insert(
				'wp_tencent_settings',
				$data,
				$format
			);

		} else {
			self::updateSetting($setting, $value, $isNetworkWide);
		}
	}

	public static function removeSetting($setting, $isNetworkWide = false)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
		if (self::settingExists($setting, $isNetworkWide)) {
			$deleteSql = "DELETE FROM `$tableName` WHERE setting = '$setting' and siteId = " . TenCentDao::getSiteId($isNetworkWide) . ";";
			return $wpdb->query($deleteSql);
		}
	}

	/**
	 * Retrieves a TCM setting from the MySQL database
	 *
	 *
	 * @param string $setting name of the setting value to retrieve
	 * @param bool $isNetworkWide if 'true' will ignore the siteId defaults to false
	 * @return string the setting value
	 */
	public static function getSetting($setting, $isNetworkWide = false)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
		if (self::settingExists($setting, $isNetworkWide)) {
			$settingRow = $wpdb->get_row("SELECT * FROM $tableName WHERE setting = '$setting' and siteId = " . TenCentDao::getSiteId($isNetworkWide) . ";");
			return $settingRow->value;
		} else {
			return '';
		}
	}

	public static function settingExists($setting, $isNetworkWide = false)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
		$sql = "SELECT * FROM $tableName WHERE setting = '$setting' and siteId = " . TenCentDao::getSiteId($isNetworkWide) . ";";
		$settingRows = $wpdb->get_row($sql);
		return (sizeof($settingRows) == 0) ? false : true;
	}

	public static function updateSetting($setting, $value, $isNetworkWide = false)
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
		if (self::settingExists($setting, $isNetworkWide)) {

			$settingRow = $wpdb->get_row("SELECT * FROM $tableName WHERE setting = '$setting' and siteId = " . TenCentDao::getSiteId($isNetworkWide) . ";");
			$rowId = $settingRow->id;

			$wpdb->update(
				$tableName,
				array(
					"setting" => $setting,
					"value" => $value
				),
				array(
					"ID" => $rowId,
					"siteId" => TenCentDao::getSiteId($isNetworkWide)
				),
				array(
					"%s",
					"%s"
				)
			);

		} else {
			self::addSetting($setting, $value, $isNetworkWide);
		}
	}

	public static function data($type)
	{
		$data = (object)array();
		switch ($type) {
			case TenDaoUtil::SUBSCRIBERS :
				$data->subscribers = self::getAllSubscriptions();
				break;
			case TenDaoUtil::UNSUBSCRIBERS :
				$data->unsubscribers = self::getAllUnsubscribers();
				break;
			case TenDaoUtil::TRACK_DATA :
				$data->tracks = self::getAllTracking();
				break;
			case TenDaoUtil::ALL_DATA :
				$data->subscribers = self::getAllSubscriptions();
				$data->unsubscribers = self::getAllUnsubscribers();
				$data->tracks = self::getAllTracking();
				break;
			default :
				//TODO : send error
				$data->subscribers = self::getAllSubscriptions();
				$data->unsubscribers = self::getAllUnsubscribers();
				$data->tracks = self::getAllTracking();
				break;
		}
		return $data;
	}

	public static function getAllSubscriptions()
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		$querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status = " . TenDaoUtil::SUBSCRIBED . " and siteId = " . TenCentDao::getSiteId(), ARRAY_A);
		$results = $wpdb->get_results($querySQL);
		return $results;
	}

	public static function getAllUnsubscribers()
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
		$querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status = " . TenDaoUtil::UNSUBSCRIBED . " and siteId = " . TenCentDao::getSiteId(), ARRAY_A);
		$results = $wpdb->get_results($querySQL);
		return $results;
	}

	public static function getAllTracking()
	{
		global $wpdb;
		$tableName = TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE);
		$querySQL = $wpdb->prepare("SELECT * FROM `$tableName` where siteId = " . TenCentDao::getSiteId(), ARRAY_A);
		$results = $wpdb->get_results($querySQL);
		return $results;
	}

	public static function createTables()
	{
		self::createTable(TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE), TenDaoUtil::getSubscriptionTableSQL(), 'Subscribe');
		self::createTable(TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE), TenDaoUtil::getTrackingTableSQL(), 'Tracking');
		self::createTable(TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE), TenDaoUtil::getSettingsTableSQL(), 'Settings');
		self::createTable(TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE), TenDaoUtil::getListsTableSQL(), 'Lists');
		self::createTable(TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE), TenDaoUtil::getContactListSettingsTableSQL(), 'Contact List Settings');
		self::createTable(TenDaoUtil::getTableName(TenDaoUtil::SNS_MESSAGE_TABLE), TenDaoUtil::getSnsMessageTableSQL(), 'Amazon SNS Messages');
	}

	public static function addSiteIdToExistingTables()
	{
		self::safelyAddColumn(TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE), "siteId", "int(11)");
		self::safelyAddColumn(TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE), "siteId", "int(11)");
		self::safelyAddColumn(TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE), "siteId", "int(11)");
		self::safelyAddColumn(TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE), "siteId", "int(11)");
		self::safelyAddColumn(TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE), "siteId", "int(11)");
		self::safelyAddColumn(TenDaoUtil::getTableName(TenDaoUtil::SNS_MESSAGE_TABLE), "siteId", "int(11)");
	}

	/**
	 * Safely adds a column to a MySQL database
	 *
	 *
	 * @param string $tableName name of the table to update
	 * @param string $columnName name of the column to update
	 * @param string $columnDefinition definition of the column to update ex: "my_additional_column varchar(2048) NOT NULL DEFAULT"
	 */
	public static function safelyAddColumn($tableName, $columnName, $columnDefinition)
	{
		global $wpdb;
		$sql =
			"IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
                AND COLUMN_NAME='$columnName' AND TABLE_NAME='$tableName') ) THEN
                ALTER TABLE $tableName ADD $columnName $columnDefinition '';
			END IF;";
		$wpdb->query($sql);
	}

	public static function createTable($tableName, $sql, $description)
	{
		if (!TenDaoUtil::tableExists($tableName)) {
			global $wpdb;
			$wpdb->query($sql);
		} else {
			throw new Exception('TenCentMail Plugin ' . $description . ' Table already exists.  Deactivate or drop the existing table for a fresh install');
		}
	}

	public static function dropTables()
	{
		self::dropTable(TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE));
		self::dropTable(TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE));
		self::dropTable(TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE));
		self::dropTable(TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE));
		self::dropTable(TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE));
		self::dropTable(TenDaoUtil::getTableName(TenDaoUtil::SNS_MESSAGE_TABLE));
	}

	public static function dropTable($tableName)
	{
		if (TenDaoUtil::tableExists($tableName)) {
			global $wpdb;
			$sql = "DROP TABLE " . $tableName;
			$dropSQL = $wpdb->prepare($sql);
			$wpdb->query($dropSQL);
		} else {
			throw new Exception('TenCentMail Plugin ' . $tableName . ' Table doesnt exist.');
		}
	}
}
