<?php

class TenCentDao
{

    public static function saveSnsMessage($type, $message)
    {
        global $wpdb;
        $table = TenDaoUtil::getTableName(TenDaoUtil::SNS_MESSAGE_TABLE);
        $type = self::getMessageType($type);
        $data = array(
            "date" => date('Y-m-d H:i:s'),
            "type" => $type,
            "message" => "$message"
        );

        $types = array("%s", "%s");
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
            "date" => date('Y-m-d H:i:s')
        );
        $types = array("%s", "%s", "%s", "%s", "%s", "%s", "%s");
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


        $prefix = "";
        $count = 0;
        $insertSqlRaw = "INSERT INTO $tableName (list, active) values ";
        foreach ($dbListsObj as $key => $contactList) {
            if (!$contactList->update && $contactList->add) {
                $count++;
                $insertSqlRaw .= $prefix . "('" . trim($contactList->list) . "', " . Utils::TRUE . ")";
                $prefix = ",";
            } else {
                array_push($inactiveLists, $contactList);
            }
        }


        foreach ($inactiveLists as $contactList) {
            $status = ($contactList->newStatus == Utils::TRUE ? Utils::TRUE : Utils::FALSE);
            $rawSql = "UPDATE $tableName set active=" . $status . " where id=$contactList->id;";
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
        return self::getContactLists("");
    }

    public static function getActiveContactLists()
    {
        return self::getContactLists("WHERE active=0");
    }

    public static function getInActiveContactLists()
    {
        return self::getContactLists("WHERE active=1");
    }

    public static function getContactLists($whereClause)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
        $sql = "SELECT * FROM $tableName  $whereClause ORDER BY active DESC;";
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
            // echo "<p>" . $id . " : " . $list . " -> " . $obj->$list->setInactive  .  "</p>";
        }
        return $obj;
    }

    public static function getContactListByList($list)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
        $sql = "SELECT * FROM $tableName WHERE list='$list';";
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
        $contactListRow = $wpdb->get_row("SELECT * FROM $tableName WHERE id='$id';");
        return (sizeof($contactListRow) == 0) ? false : true;
    }

    public static function deleteContactListSettings($listId)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
        $deleteSql = "DELETE FROM `$tableName` WHERE listId=$listId;";
        return $wpdb->query($deleteSql);
    }

    public static function deleteContactList($id)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
        if (self::contactListExists($id)) {
            $deleteSql = "DELETE FROM `$tableName` WHERE id='$id';";
            return $wpdb->query($deleteSql);
        }
    }

    public static function contactListExistsByList($list)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LISTS_TABLE);
        $contactListRow = $wpdb->get_row("SELECT * FROM $tableName WHERE list='$list';");
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
        $query = "SELECT * FROM `$table` WHERE listId=$listId";

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
        $query = "SELECT * FROM `$table` WHERE listId='$listId'";
        $results = $wpdb->query($query);
        return ($results > 0) ? true : false;
    }

    public static function updateContactListSettings($listId, $settings)
    {
        global $wpdb;

        $table = TenDaoUtil::getTableName(TenDaoUtil::CONTACT_LIST_SETTINGS_TABLE);
        $dataTypes = self::getSettingsDataTypes($settings);
        $settingsId = self::getSettingsId($listId);
        $where = array("ID" => $settingsId);

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
        $row = $wpdb->get_row("SELECT * FROM $table WHERE listId='$listId'", ARRAY_A);
        return $row['id'];
    }

    public static function confirmOptIn($email, $list)
    {

        global $wpdb;
        if (self::isSubscribed($email, $list)) {

            $tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
            $data = array("confirmedDoubleOpt" => Utils::TRUE);

            $where = array("ID" => self::getSubscriptionRowId($email, $list));
            $format = '%d';
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
        $sql = "SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::SUBSCRIBED . " and email='$email' and list='$list'";
        $rows = $wpdb->get_row($sql);
        return (sizeof($rows) == 0) ? false : true;
    }

    public static function isUnSubscribed($email, $list)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
        $sql = "SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::UNSUBSCRIBED . " and email='$email' and list='$list'";
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
        return $obj;
    }

    public static function getSubscriptionRowId($email, $list)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
        $row = $wpdb->get_row("SELECT * FROM $tableName WHERE email='$email' AND list='$list'", ARRAY_A);
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

            $subscription = $wpdb->get_row("SELECT * FROM " . $table . " WHERE email='" . $config->email->value . "' AND list='" . $config->list->value . "';");

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

//    public static function delete($type, $rowId)
//    {
//        $table;
//        if (self::tableExistsForType($type)) {
//            $table = self::typeTableMapping()->$type;
//        } else {
//            //throw new Exception("The type $type does not match anything defined.");
//        }
//
//        if (self::dataRowExists($table, $rowId)) {
//            global $wpdb;
//            $row = intval($rowId);
//            $deleteSql = "DELETE FROM `$table` WHERE id <= $row " . self::getStatusClause($type);
//            return $wpdb->query($deleteSql);
//        } else {
//            //throw new Exception("The row id specified does not exist");
//        }
//
//    }

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
        $results = $wpdb->query("SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::SUBSCRIBED . " and email='$email' and list='$list'");

        return ($results == 0) ? true : false;
    }

    public static function getSubscriptions($list)
    {
        global $wpdb;
        $results = 0;
        if (isset($list)) {
            $tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
            $querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::SUBSCRIBED . " and list='$list'", ARRAY_A);
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
            $querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::UNSUBSCRIBED . " and list='$list'", ARRAY_A);
            $results = $wpdb->get_results($querySQL);
        }
        return $results;
    }

    public static function unsubscribe($email, $list, $campaignId)
    {
        global $wpdb;

        $stmt = "";
        $result = "";
        $now = date('Y-m-d H:i:s');
        $table = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
        //$wpdb->show_errors();
        if (self::isSubscribed($email, $list) || self::isUnSubscribed($email, $list)) {
            //clrg 2/12/13 - updated the date when the record is updated
            $sql = "UPDATE `$table` SET status=" . TenDaoUtil::UNSUBSCRIBED . ", campaignId=$campaignId, `date`='$now' WHERE email='$email' and list='$list'";
            //echo $sql."<br/>";
            $stmt = $wpdb->prepare($sql);
            $result = $wpdb->query($stmt);
            //var_dump($result);

            //}else if(!self::isUnSubscribed($email, $list)){
        } else {
            $data = array(
                "email" => $email,
                "list" => $list,
                "campaignId" => $campaignId,
                "date" => $now,
                "status" => TenDaoUtil::UNSUBSCRIBED,
                "requiresDoubleOpt" => Utils::FALSE
            );

            //clrg 2/11/13 - fixed exception due to wrong type in array
            //$types = array("%s", "%s", "%s", "%d", "%d", "%d");
            $types = array("%s", "%s", "%d", "%s", "%d", "%d");
            $result = $wpdb->insert($table, $data, $types);
            // clrg 2/12/13 - removed exception when a subscriber unsubscribes more than once
//		}else{
//			throw new Exception("email has already been unsubscribed");
        }

        try {
            return $result;
        } catch (Exception $e) {
            throw new Exception("something went wrong performing save");
        }
    }

    public static function addSetting($setting, $value)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
        if (!self::settingExists($setting)) {

            $data = array(
                "setting" => $setting,
                "value" => $value
            );
            $format = array(
                "%s",
                "%s"
            );
            return $wpdb->insert(
                'wp_tencent_settings',
                $data,
                $format
            );

        } else {
            self::updateSetting($setting, $value);
        }
    }

    public static function removeSetting($setting)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
        if (self::settingExists($setting)) {
            $deleteSql = "DELETE FROM `$tableName` WHERE setting='$setting';";
            return $wpdb->query($deleteSql);
        }
    }

    public static function getSetting($setting)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
        if (self::settingExists($setting)) {
            $settingRow = $wpdb->get_row("SELECT * FROM $tableName WHERE setting='$setting';");
            return $settingRow->value;
        } else {
            return '';
        }
    }

    public static function settingExists($setting)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
        $sql = "SELECT * FROM $tableName WHERE setting='$setting';";
        $settingRows = $wpdb->get_row($sql);
        return (sizeof($settingRows) == 0) ? false : true;
    }

    public static function updateSetting($setting, $value)
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SETTINGS_TABLE);
        if (self::settingExists($setting)) {

            $settingRow = $wpdb->get_row("SELECT * FROM $tableName WHERE setting='$setting';");
            $rowId = $settingRow->id;

            $wpdb->update(
                $tableName,
                array(
                    "setting" => $setting,
                    "value" => $value
                ),
                array(
                    "ID" => $rowId
                ),
                array(
                    "%s",
                    "%s"
                )
            );

        } else {
            self::addSetting($setting, $value);
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
        $querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::SUBSCRIBED, ARRAY_A);
        $results = $wpdb->get_results($querySQL);
        return $results;
    }

    public static function getAllUnsubscribers()
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::SUBSCRIBE_TABLE);
        $querySQL = $wpdb->prepare("SELECT * FROM `$tableName` WHERE status=" . TenDaoUtil::UNSUBSCRIBED, ARRAY_A);
        $results = $wpdb->get_results($querySQL);
        return $results;
    }

    public static function getAllTracking()
    {
        global $wpdb;
        $tableName = TenDaoUtil::getTableName(TenDaoUtil::TRACKING_TABLE);
        $querySQL = $wpdb->prepare("SELECT * FROM `$tableName`", ARRAY_A);
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
            throw new Exception('TenCentMail Plugin ' . $tableName . ' Table doesnt exists.');
        }
    }
}
