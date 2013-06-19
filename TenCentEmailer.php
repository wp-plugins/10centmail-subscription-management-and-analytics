<?php 

class TenCentEmailer {

	const DEFAULT_LINK_CONTENT = "Click Here to Confirm";	

	public static function sendSubscribeEmails($config, $list){
		self::sendSubscriptionNotifier($config);
		$config->requires_double_opt->value == "true" ? self::sendConfirmationEmail($config, $list) : self::sendSubscriptionThankyou($config, $list);
	}

	
	public static function sendDoubleOptEmails($config, $list){
		self::sendDoubleOptNotifier($config);
		self::sendDoubleOptThankyou($config, $list);
	}
	
	
	public static function sendSubscriptionNotifier($config){		
		$message = self::getMessage(self::getNotifierTemplate($config), $config);					
		$subject = TenCentDao::getSetting("tencentmail_company_name") . " - New Subscriber";
		$to = TenCentDao::getSetting("tencentmail_notification_emails");
		if(!empty($to))self::sendEmail($to, $subject, $message);		
	}

	
	public static function sendConfirmationEmail($config, $list){
	
		$listSettings = TenCentDao::getContactListSettings($list->id);
		
		$templateData = (object)array(
			"opt_in_confirmation_link" => self::getDoubleOptConfirmationLink($config),
			"company_name" => TenCentDao::getSetting("tencentmail_company_name")
		);
			
		$linkContent = $listSettings['double_opt_in_confirmation_link_content'];
		if(empty($linkContent))$linkContent = self::DEFAULT_LINK_CONTENT;
		
		$templateData->link_content = $linkContent;
		$message = self::getMessage(self::getDefaultConfirmationTemplate(), $templateData);
		
		$subject = $listSettings['double_opt_in_confirmation_subject'];
		
		if(empty($subject))$subject = TenCentDao::getSetting("tencentmail_company_name") . " Please Confirm Opt-In Status";
					
		$to = $config->email->value;
		self::sendEmail($to, $subject, $message);
	}
	
	
	
	public static function sendSubscriptionThankyou($config, $list){		

		$listSettings = TenCentDao::getContactListSettings($list->id);

		$subject =  $listSettings['thank_you_subscribe_subject'];
		
		if(empty($subject))$subject = TenCentDao::getSetting("tencentmail_company_name") . ' Successfully Signed up';
		
		$message = $listSettings['thank_you_subscribe_message'];
		
		if(empty($message))$message = 'Thank you for signing up';
		
		$trackingLink = self::getFullTrackingLink();
		$message .= $trackingLink;
		
		$unsubscribeLinkText = $listSettings['unsubscribe_link_text'];
		
		if(empty($unsubscribeLinkText))$unsubscribeLinkText = 'unsubscribe to ALL future ' . TenCentDao::getSetting("tencentmail_company_name") . ' newsletters';
		
		
		$unsubscribeLink = self::getFullUnsubscribeLink($config->email->value, $config->list->value, $unsubscribeLinkText);
		$message .= $unsubscribeLink;
		
		
		$to = $config->email->value;		
		self::sendEmail($to, $subject, $message);
	}
	
	
	public static function sendDoubleOptNotifier($config){
		$message = self::getMessage(self::getDoubleOptNotifierTemplate(), $config);
		$subject = "10CentMail - Double Opt Confirmed";
		$to = TenCentDao::getSetting("tencentmail_notification_emails");
		self::sendEmail($to, $subject, $message);
	}
	
	
	public static function sendDoubleOptThankyou($config, $list){
		
		$listSettings = TenCentDao::getContactListSettings($list->id);
		
		$message = $listSettings['thank_you_double_opt_in_message'];
		if(empty($message))$message = 'Thank you for confirming your subscription';
		
		$subject =  $listSettings['thank_you_double_opt_in_subject'];
		if(empty($subject))$subject = TenCentDao::getSetting("tencentmail_company_name") . ' Successfully Confirmed';
		
		
		$unsubscribeLinkText = $listSettings['unsubscribe_link_text'];
		if(empty($unsubscribeLinkText))$unsubscribeLinkText = 'unsubscribe to ALL future ' . TenCentDao::getSetting("tencentmail_company_name") . ' newsletters';
		$unsubscribeLink = self::getFullUnsubscribeLink($config->email, $config->list, $unsubscribeLinkText);
		$message .= $unsubscribeLink;
		
		$to = $config->email;
		self::sendEmail($to, $subject, $message);
	}


	
	private static function getDefaultConfirmationTemplate(){
		return '' .
			'<h2>{{company_name}}</h2>' .
			'<p>Please confirm you opt in to receive emails from us</p>' .
			'<p><a href="{{opt_in_confirmation_link}}">{{link_content}}</a></p>';
	}

	
	private static function getDoubleOptNotifierTemplate(){
		return '<h2>Someone Confirmed Double Opt</h2>' .
			'<p><label>Email : </label>{{email}} </p>' .
			'<p><label>List : </label>{{list}} </p>';
	}
	
	
	
	
	private static function getNotifierTemplate($config){				
		$template = '<h2>Someone signed up</h2>';
		foreach($config as $property=>$value){
			if($property != "request_uri" && $property != "requires_double_opt"){
				$template .= '<p><label>' . $config->$property->label .'</label> : ' . $config->$property->value . '</p>';
			}
		}
		return $template;		
	}

	
	private static function getThankyouTemplate(){
		return ' ' .
			'<img src="{{additional.tracking_link}}" width="1" height="1"/>'.
			'<h2>Thank you</h2>';
	}
	
	
	private static function getDoubleOptConfirmationLink($config){
		$params = "&list=" . $config->list->value . "&email=" . $config->email->value;
		return site_url() . '/?10cent=confirm_double_opt' . $params;
	}
	
	private static function getFullUnsubscribeLink($email, $list, $text){
		return '<p><a href="' . self::getUnsubscribeLink($email, $list) . '" title="' . $text . '">' . $text . '</a></p>';
	}
	
	private static function getUnsubscribeLink($email, $list){
		$params = "&list=" . $list . "&email=" . $email;
		return site_url() . '/?10cent=unsubscribe_form' . $params;
	}
	
	
	private static function getFullTrackingLink(){
		$url = self::getTrackingLink();
		return '<img src="' . $url . '"/>';
	}
		
		
	private static function getTrackingLink(){
		$params = "&trackingId=" . Utils::uuid() . "&type=image";
		return site_url() . '/?10cent=track' . $params;
	}
	
	
	public static function getMessage($template, $config){
		$m = new Mustache();
		return $m->render($template, $config);
	}
	
	
	public static function sendEmail($to, $subject, $message){
		add_filter('wp_mail_content_type', create_function('', 'return "text/html"; '));		
		add_filter('wp_mail_from', create_function('', 'return "' . TenCentDao::getSetting('tencentmail_from_email') . '";'));
		add_filter('wp_mail_from_name', create_function('', 'return "' . TenCentDao::getSetting('tencentmail_from_name') . '";'));		
		$sent = wp_mail($to, $subject, $message);	
	}
	
		
}
