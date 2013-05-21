<?php

class Utils
{

	const TRUE = 1;
	const FALSE = 0;
	const SUBSCRIBED = 1;
	const UNSUBSCRIBED = 0;


	public static function toObj($array)
	{
		$obj = (object)array();
		foreach ($array as $k => $v) {
			//echo "<p>" . $k." : " . $v . "</p>";
			$obj->$k = $v;
		}
		return $obj;
	}

	public static function containsTag($tag, $text)
	{
		return stripos($text, $tag) !== false ? true : false;
	}

	public static function toObjNoValue($array)
	{
		$obj = (object)array();
		foreach ($array as $k) {
			//echo "<p>" . $k." : " . $v . "</p>";
			$obj->$k = true;
		}
		return $obj;
	}


	public function validEmail($email)
	{
		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
//			list($username, $domain) = split('@', $email);
			list($username, $domain) = preg_split('@', $email);
			if (!checkdnsrr($domain, 'MX')) {
				return false;
			}
			return true;
		}
		return false;
	}


	public static function uuid($prefix = "")
	{
		$chars = md5(uniqid(rand()));
		$uuid = substr($chars, 0, 8) . '-';
		$uuid .= substr($chars, 8, 4) . '-';
		$uuid .= substr($chars, 12, 4) . '-';
		$uuid .= substr($chars, 16, 4) . '-';
		$uuid .= substr($chars, 20, 12);
		return $prefix . $uuid;
	}

}

?>