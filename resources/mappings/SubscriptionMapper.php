<?php

class SubscriptionMapper {
	
	public static function getMapper(){
		return (object)array(		
			'email' => (object)array(
				'data_column'  => 'email',
				'placeholder'  => '%s',
				'label'        => 'Email Address',
				'validators'   => array(
					'NotEmptyValidator',
					'EmailValidator'
				)
			),
			'list' => (object)array(
				'data_column'  => 'list',
				'placeholder'  => '%s',
				'label'        => 'List',
				'minmax'       => array(3, 55),
				'validators'   => array(
					'NotEmptyValidator',
					'StringLengthValidator'
				)
			),
			'first_name' =>  (object)array(
				'data_column'  => 'firstName',
				'placeholder'  => '%s',
				'label'        => 'First Name',
				'minmax'       => array(3, 55),
				'validators'   => array(
					'NotEmptyValidator',
					'StringLengthValidator',
					'NoSpecialCharactersValidator'
				)
			),
			'last_name' => (object)array(
				'data_column'  => 'lastName',
				'placeholder'  => '%s',
				'label'        => 'Last Name',
				'minmax'       => array(3, 55),
				'validators'   => array(
					'NotEmptyValidator',
					'StringLengthValidator',
					'NoSpecialCharactersValidator'
				)
			),
			'full_name' =>  (object)array(
				'data_column'  => 'fullName',
				'placeholder'  => '%s',
				'label'        => 'Full Name',
				'minmax'       => array(3, 55),
				'validators'   => array(
					'NotEmptyValidator',
					'StringLengthValidator',
					'NoSpecialCharactersValidator'
				)
			),
			'custom_field' =>  (object)array(
				'data_column'  => 'customField',
				'placeholder'  => '%s',
				'label'        => 'Custom Field',
				'minmax'       => array(1, 55),
				'validators'   => array(
					'NotEmptyValidator',
					'StringLengthValidator',
					'NoSpecialCharactersValidator'
				)
			),
			'ip' =>  (object)array(
				'data_column'  => 'ip',
				'placeholder'  => '%s',
				'label'        => 'IP Address',
				'validators'   => array(
					'NotEmptyValidator',
					'IPAddressValidator'
				)
			),
			'status' =>  (object)array(
				'data_column'  => 'status',
				'placeholder'  => '%d',
				'label'        => 'Status',
				'validators'   => array(
					'NotEmptyValidator',
				)
			),			
			'date' =>  (object)array(
				'data_column'  => 'date',
				'placeholder'  => '%s',
				'label'        => 'Date',
				'validators'   => array(
					'NotEmptyValidator',
				)
			),			
			'requires_double_opt'  =>  (object)array(
				'data_column'  => 'requiresDoubleOpt',
				'placeholder'  => '%d',
				'label'        => 'Requires Double Opt',
				'value'        => 'false',
				'validators'   => array(
					'NotEmptyValidator'
				)
			),
			'confirmed_double_opt' =>  (object)array(
				'data_column'  => 'confirmedDoubleOpt',
				'placeholder'    => '%d',
				'validators'   => array(
					'NotEmptyValidator'
				)
			)
		);
	}
	
}

?>