<?php
	
class TenCentSubscribeTmpl {

	public static function get(){
		return ' ' .
			TenCentCss::get() .
			'<div id="tencentmail_subscribe_form">' .
			'{{#messages}}' .
			'<p class="tencentmail_form_error tencentmail_error">{{message}}</p>' .
			'{{/messages}}' .	
			'{{#additional.errorMessage}}' .	
			'	<p class="tencentmail_form_error tencentmail_error">{{additional.errorMessage}}</p>' .
			'{{/additional.errorMessage}}' .				
			'<form method="post" action="{{request_uri}}">' .
			'<p class="{{email.status}}">' .
			'	<label>{{email.label}} : </label>' .
			'	<input type="text" name="email" value="{{email.value}}"/>' .
			'</p>' .
			'<input type="hidden" name="list" value="{{list.value}}"/>' .
			'<input type="hidden" name="ip" value="{{ip.value}}"/>' .
			'<input type="hidden" name="requires_double_opt" value="{{requires_double_opt.value}}"/>' . 
			'{{#first_name}}' .
			'	<p class="{{first_name.status}}">' .
			'		<label>{{first_name.label}} : </label>' .
			'		<input type="text" name="first_name" value="{{first_name.value}}"/>' .
			'	</p>' .
			'{{/first_name}}' .
			'{{#last_name}}' .
			'	<p class="{{last_name.status}}">' .
			'		<label>{{last_name.label}} : </label> ' .
			'		<input type="text" name="last_name" value="{{last_name.value}}"/>' .
			'	</p>' .
			'{{/last_name}}' .
			'{{#full_name}}' .
			'	<p class="{{full_name.status}}">' .
			'		<label>{{full_name.label}} : </label> ' .
			'		<input type="text" name="full_name" value="{{full_name.value}}"/>' .
			'	</p>' .
			'{{/full_name}}' .	
			'{{#custom_field}}' .
			'	<p class="{{custom_field.status}}">' .
			'		<label>{{custom_field.label}} : </label> ' .
			'		<input type="text" name="custom_field" value="{{custom_field.value}}"/>' .
			'	</p>' .
			'{{/custom_field}}' .
			'<p class="tencentmail_submit_wrapper"><input type="submit" value="{{additional.button_text}}"/></p>' .		
			'</form>' .
			'</div>';		
	}
	
}

?>