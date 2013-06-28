<?php

function processAction()
{
	$action = $_REQUEST['10cent'];

	if (strtoupper($_SERVER['REQUEST_METHOD']) == 'GET') {
		switch ($action) {
			case 'aws_sns' :
				processAwsSnsMessage();
				break;
			case 'confirm_double_opt' :
				confirmDoubleOpt();
				break;
			case 'contact_lists' :
				updateContactLists();
				break;
			case 'data' :
				getData();
				break;
			case 'debug' :
				include_once('actions/debug.php');
				break;
			case 'endpoints' :
				getEndpoints();
				break;
			case 'track' :
				track();
				break;
			case 'unsubscribe' :
				unsubscribe();
				break;
			case 'unsubscribe_form' :
				include_once('actions/unsubscribe_form.php');
				break;
			default :
				wp_die("Not Found");
				break;
		}
	}
}

function tenrequest($wp)
{
	if (array_key_exists('10cent', $wp->query_vars)) {
		processAction();
	}
}

function query_variables($vars)
{
	$vars[] = '10cent';
	return $vars;
}

add_action('parse_request', 'tenrequest');
add_filter('query_vars', 'query_variables');