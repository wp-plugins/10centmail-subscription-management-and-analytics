<?php

function tencentmail_endpoints_metadata() {
	//$tencentmail_wordpress_endpoints_url = TenCentDao::getSetting("tencentmail_wordpress_endpoints_url");
	$tencentmail_wordpress_endpoints_url = get_endpoints_url();
	//if(!empty($tencentmail_wordpress_endpoints_url)){
		echo "<meta name=\"tencentmail-wp-settings\" content=\"$tencentmail_wordpress_endpoints_url\"/>\n";
    //}
}


function tencentmail_version_metadata(){
    $tencentmail_plugin_version = TenCentDao::getSetting("tencentmail_version");  
    if(!empty($tencentmail_plugin_version)){
        echo "<meta name=\"tencentmail-plugin-version\" content=\"$tencentmail_plugin_version\"/>\n";
    }
}
