<?php
	if(isset($_GET['park'])){
		$park = $_GET['park'];
		$dataURL = "https://developer.nps.gov/api/v0/alerts?parkCode=$park";
	}else {
		$park = 'ACAD';
		$dataURL = 'https://developer.nps.gov/api/v0/alerts?parkCode=acad';
	}
	
	// Get cURL resource
	$curl = curl_init();
	// Set options
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_URL => $dataURL,
		CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
		CURLOPT_HTTPHEADER => array('Authorization: INSERT-API-KEY-HERE')
	));
	// Send the request & save response to $response
	$response = curl_exec($curl);
	// Close request to clear up some resources
	curl_close($curl);
	$json = json_decode($response);
	
	// Set variables
	$alertsTotal = $json->total;
	$alertsCount = 0;
	$isoDate = gmdate("Y-m-d\TH:i:s\Z");
	
	// Function to write json for each alert type
	function outputAlert($alertType) {
		global $alertsTotal, $alertsCount, $json, $isoDate;
		for ($i = 0; $i < count($json->data); $i++) {
			if ($json->data[$i]->category == $alertType) {
				$alertsCount = $alertsCount + 1;
				echo '{';
				echo '"uid": "', $json->data[$i]->id, '",';
				echo '"updateDate": "', $isoDate, '",';
				echo '"titleText": ', json_encode($alertType . ' Alert: ' . $json->data[$i]->title), ',';
				if ($json->data[$i]->url != '') {
					echo '"mainText": ', json_encode($json->data[$i]->description), ',';
					echo '"redirectionUrl": "', $json->data[$i]->url, '"';
				} else {
					echo '"mainText": ', json_encode($json->data[$i]->description);
				}
				if ($alertsTotal > $alertsCount) {
					echo '},';
				} else {
					echo '}';
				}
			}
		}
	}
	
	if ($alertsTotal > 0) {	
		// Start outputting json if there are alerts
		header('Content-Type: application/json');
		echo '[';
		
		// Output danger alerts
		outputAlert("Danger");
		
		// Output park closure alerts
		outputAlert("Park Closure");
		
		// Output caution alerts
		outputAlert("Caution");
		
		// Output information alerts
		outputAlert("Information");
		
		echo ']';
	} else {
		// Start outputting json if there are no alerts
		header('Content-Type: application/json');
		echo '[';
		echo '{';
		echo '"uid": "', uniqid('',true), '",';
		echo '"updateDate": "', $isoDate, '",';
		echo '"titleText": "No active alerts.",';
		echo '"mainText": "There are no active alerts at this time. Please visit the website for information about news and events.",';
		echo '"redirectionUrl": "https://www.nps.gov/', $park, '"';
		echo '}';
		echo ']';
	}
?>