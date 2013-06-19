<?php

class IPAddressValidator implements IValidator
{
	public function valid($ipAddress)
	{

		//first of all the format of the ip address is matched
		if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ipAddress)) {

			$parts = explode(".", $ipAddress);

			//now we need to check each part can range from 0-255
			foreach ($parts as $ip_parts) {
				if (intval($ip_parts) > 255 || intval($ip_parts) < 0)
					return false;
			}

			return true;

		} else {
			return false;
		}
	}

	public function error($ipAddress, $label)
	{
		return $ipAddress . " is not a valid ip address";
	}

}
?>