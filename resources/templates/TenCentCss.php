<?php

class TenCentCss {
	
	public static function get(){
		return ''.
			'<style type="text/css">' .
				'#tencentmail_subscribe_form {'.
				'	text-align:left;'.
				'}'.
				'p.tencentmail_form_error { '. 
					'background:#feeaea; '.
					'font-size:12px; '. 
					'line-height:1.5em; '.
					'vertical-align:middle; '.
					'margin:4px auto; '.
					'padding:3px;'.
					'border:solid 1px #fabdbd; '.
				'}'.
				'p.tencentmail_form_success {'.
					'background:#d7fcd7; '.
					'font-size:12px; '. 
					'line-height:1.5em; '.
					'vertical-align:middle; '.
					'margin:4px auto; '.
					'padding:3px;'.
					'border:solid 1px #c4f1c4; '.
				'}'.				
				'label{ display:block }' .
			'</style>';
	}

}

?>