<?php

session_start();

class TenCentForm
{

	public static function renderSubscribeForm($config)
	{
		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
			$formObjectWithData = self::setValues();
			$dataValidator = new DataValidator($formObjectWithData);
			$dataValidator->validate();

			if ($dataValidator->valid()) {

				try {

					$list = TenCentDao::getContactListByList($dataValidator->data->list->value);
					TenCentDao::saveSubscriber($dataValidator->data);
					TenCentEmailer::sendSubscribeEmails($dataValidator->data, $list);

					// redirect if the redirect_url value is set
					$mixedMapping = self::getMixedMapping(Utils::toObj($config));
					if (!empty($mixedMapping->redirect_url)) {
						wp_redirect($mixedMapping->redirect_url);
						die();
					}

					$listSettings = TenCentDao::getContactListSettings($list->id);
					$listSuccessContent = $listSettings['subscribe_success_message'];

					if (!empty($listSuccessContent)) {
						return $listSuccessContent;
					} else {
						return TenCentCss::get() .
							'<p class="tencentmail_form_success">You have successfully submitted your information</p>';
					}

				} catch (Exception $e) {
					if (empty($dataValidator->data->additional)) $dataValidator->data->additional = (object)array();
					$dataValidator->data->additional->errorMessage = $e->getMessage();
					return self::getFormHtml($dataValidator->data);
				}

			} else {
				return self::getFormHtml($dataValidator->data);
			}
		} else {
			$configObj = Utils::toObj($config);
			$formDataObject = self::getMixedMapping($configObj);

			if (!empty($formDataObject->list->value) &&
				TenCentDao::contactListExistsByList($formDataObject->list->value)) {
				return self::getFormHtml($formDataObject);
			} else {
				return TenCentCss::get() .
					'<p class="tencentmail_form_error">list is either missing from the short code or the list passed does not exist.<br/>' .
					'ex: [tencentmail_subscribe_form list="listname"]</p>';
			}
		}
	}


	public static function setValues()
	{
		$formObject = $_SESSION['tcm'];
		unset($formObject->messages);
		unset($formObject->additional);
		foreach ($_POST as $key => $value) {
			$formObject->$key->value = $value;
		}

		$ip = $_SERVER['REMOTE_ADDR'];
		if ($ip == "::1") {
			$ip = "0.0.0.0";
		}

		$formObject->ip->value = $ip;

		$formObject->status->value = Utils::SUBSCRIBED;
		$formObject->date->value = date('Y-m-d H:i:s');
		return $formObject;
	}


	public static function getMixedMapping($options)
	{
		$baseSubscriptionMapper = SubscriptionMapper::getMapper();
		$mixedMapping = self::getBaseMixedMapping($baseSubscriptionMapper);

		foreach ($options as $property => $value) {
			if ($value && !empty($baseSubscriptionMapper->$property)) {
				if ($property == 'list') {
					$mixedMapping->list->value = $value;
					continue;
				}
				if ($property == 'requires_double_opt') {
					$mixedMapping->requires_double_opt->value = $value;
					continue;
				}
				if ($value == "true") $mixedMapping->$property = $baseSubscriptionMapper->$property;
			}
			if ($property == 'custom_label')
			{
				$mixedMapping->custom_field = $baseSubscriptionMapper->custom_field;
				$mixedMapping->custom_field->label = $value;
			}
			if ($property == 'redirect_url') $mixedMapping->redirect_url = $value;
		}

		if ($options->double_opt == "true") $mixedMapping->requires_double_opt->value = "true";

		return $mixedMapping;
	}


	public static function getBaseMixedMapping($baseSubscriptionMapper)
	{
		$baseMixed = (object)array();
		$baseMixed->email = $baseSubscriptionMapper->email;
		$baseMixed->list = $baseSubscriptionMapper->list;
		$baseMixed->ip = $baseSubscriptionMapper->ip;
		$baseMixed->date = $baseSubscriptionMapper->date;
		$baseMixed->status = $baseSubscriptionMapper->status;
		$baseMixed->requires_double_opt = $baseSubscriptionMapper->requires_double_opt;
		$baseMixed->redirect_url = "";

		return $baseMixed;
	}


	public static function getFormHtml($config)
	{
		$_SESSION['tcm'] = $config;
		$m = new Mustache();

		$list = TenCentDao::getContactListByList($config->list->value);

		$listSettings = TenCentDao::getContactListSettings($list->id);
		$buttonText = $listSettings['subscribe_button_text'];

		if (empty($buttonText)) $buttonText = "Submit";
		if (empty($config->additional)) $config->additional = (object)array();
		$config->additional->button_text = $buttonText;

		$config->request_uri = $_SERVER['REQUEST_URI'];
		$html = $m->render(TenCentSubscribeTmpl::get(), $config);
		return ' ' . (string)$html;
	}
}
