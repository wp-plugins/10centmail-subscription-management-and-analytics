<?php

class EmailValidator implements IValidator
{

	public function valid($email)
	{
		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
			list($username, $domain) = explode('@', $email);
			if (!checkdnsrr($domain, 'MX')) {
				return false;
			}
			return true;
		}
		return false;
	}

	public function error($email, $label)
	{
		return $label . " is not a valid email";
	}

}
