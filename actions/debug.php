<html>
<head>

	<?php

	tencentmail_endpoints_metadata();
	tencentmail_version_metadata();

	//$css = str_replace("actions/debug.php", "resources/css/debug.css", plugin_basename(__FILE__));
//	$css = MY_PLUGIN_NAME . "/resources/css/debug.css";
//	echo '<link rel="stylesheet" type="text/css" href="' . plugins_url() . '/' . $css . '"/>';
	$css = "resources/css/debug.css";
	echo '<link rel="stylesheet" type="text/css" href="' . MY_PLUGIN_BASE_URL . $css . '"/>';

	$response = (object)array();
	$data = TenCentDao::data($dataType);

	$tencentmail_key = TenCentDao::getSetting("tencentmail_key");
	$requestKey = $_REQUEST['tcmKey'];


	if ($requestKey != "" &&
	$tencentmail_key != "" &&
	isset($tencentmail_key) &&
	isset($requestKey) &&
	$tencentmail_key == $requestKey){

	?>


</head>
<body>

<div id="debug">

	<h2 class="debug">TEMPORARY DATA</h2>

	<div id="subscribers-data-tab" class="tencent-tabcontent">
		<div class="tab-innercontent">
			<div class="settings-wrapper containslists">
				<h2>Subscribers</h2>
				<?php
				if (count($data->subscribers) > 0) {

					echo "<table>";
					echo "<tr><th>id</th><th>email</th><th>first name</th><th>last name</th><th>full name</th><th>custom</th><th>list</th><th>status</th><th>date</th><th>campaignId</th><th>ip</th><th>confirmed double</th><th>requires double</th></tr>";
					foreach ($data->subscribers as $subscription) {
						echo "<tr>";
						echo "<td>" . $subscription->id . "</td>";
						echo "<td>" . $subscription->email . "</td>";
						echo "<td>" . $subscription->firstName . "</td>";
						echo "<td>" . $subscription->lastName . "</td>";
						echo "<td>" . $subscription->fullName . "</td>";
						echo "<td>" . $subscription->customField . "</td>";
						echo "<td>" . $subscription->list . "</td>";
						echo "<td>" . $subscription->status . "</td>";
						echo "<td>" . $subscription->date . "</td>";
						echo "<td>" . $subscription->campaignId . "</td>";
						echo "<td>" . $subscription->ip . "</td>";
						echo "<td>" . $subscription->confirmedDoubleOpt . "</td>";
						echo "<td>" . $subscription->requiresDoubleOpt . "</td>";
						echo "</tr>";
					}
					echo "</table>";

				} else {
					echo "<h4>No Subscribers</h4>";
				}

				?>
			</div>
			<div id="tencentmail-no-lists-wrapper" style="display:none" class="critical nolists">
				<h2 class="critical-setting">Open the 10CentMail Desktop App and Create a new Contact List</h2>

				<p>Contact Lists must be synced with the plugin to continue</p>
			</div>
		</div>
	</div>


	<div id="unsubscribers-data-tab" class="tencent-tabcontent">
		<div class="tab-innercontent">
			<div class="settings-wrapper containslists">
				<h2>Unsubscribers</h2>
				<?php

				if (count($data->unsubscribers) > 0) {

					echo "<table>";
					echo "<tr><th>id</th><th>email</th><th>first name</th><th>last name</th><th>full name</th><th>custom</th><th>list</th><th>status</th><th>date</th><th>campaignId</th><th>ip</th><th>confirmed double</th><th>requires double</th></tr>";
					foreach ($data->unsubscribers as $subscription) {
						echo "<tr>";
						echo "<td>" . $subscription->id . "</td>";
						echo "<td>" . $subscription->email . "</td>";
						echo "<td>" . $subscription->firstName . "</td>";
						echo "<td>" . $subscription->lastName . "</td>";
						echo "<td>" . $subscription->fullName . "</td>";
						echo "<td>" . $subscription->customField . "</td>";
						echo "<td>" . $subscription->list . "</td>";
						echo "<td>" . $subscription->status . "</td>";
						echo "<td>" . $subscription->date . "</td>";
						echo "<td>" . $subscription->campaignId . "</td>";
						echo "<td>" . $subscription->ip . "</td>";
						echo "<td>" . $subscription->confirmedDoubleOpt . "</td>";
						echo "<td>" . $subscription->requiresDoubleOpt . "</td>";
						echo "</tr>";
					}
					echo "</table>";

				} else {
					echo "<h4>No unsubscribers</h4>";
				}

				?>
			</div>
			<div id="tencentmail-no-lists-wrapper" style="display:none" class="critical nolists">
				<h2 class="critical-setting">Open the 10CentMail Desktop App and Create a new Contact List</h2>

				<p>Contact Lists must be synced with the plugin to continue</p>
			</div>
		</div>
	</div>


	<div id="tracking-data-tab" class="tencent-tabcontent">
		<div class="tab-innercontent">
			<div class="settings-wrapper containslists">
				<h2>Tracking Data</h2>
				<?php
				if (count($data->tracks) > 0) {

					echo "<table>";
					echo "<tr><th>id</th><th>tracking id</th><th>type</th><th>ip</th><th>url</th><th>agent</th><th>referer</th><th>date</th></tr>";
					foreach ($data->tracks as $track) {
						echo "<tr>";
						echo "<td>" . $track->id . "</td>";
						echo "<td class=\"tracking\">" . $track->trackingId . "</td>";
						echo "<td>" . $track->type . "</td>";
						echo "<td>" . $track->ip . "</td>";
						echo "<td>" . $track->url . "</td>";
						echo "<td class=\"agent\">" . $track->agent . "</td>";
						echo "<td>" . $track->referer . "</td>";
						echo "<td>" . $track->date . "</td>";
						echo "</tr>";
					}
					echo "</table>";

				} else {
					echo "<h4>No Tracking Data</h4>";
				}
				?>
			</div>
			<div id="tencentmail-no-lists-wrapper" style="display:none" class="critical nolists">
				<h2 class="critical-setting">Open the 10CentMail Desktop App and Create a new Contact List</h2>

				<p>Contact Lists must be synced with the plugin to continue</p>
			</div>
		</div>
	</div>

</div>



<?php

die();

} else {
	wp_die("Resource not found");
}

?>


</body>
</html>