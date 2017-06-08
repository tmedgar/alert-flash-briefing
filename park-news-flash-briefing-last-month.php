<?php
	if(isset($_GET['park'])){
		$park = $_GET['park'];
		$dataURL = "https://developer.nps.gov/api/v0/newsreleases?parkCode=$park";
	}else {
		$park = 'ACAD';
		$dataURL = 'https://developer.nps.gov/api/v0/newsreleases?parkCode=acad';
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
	$newsTotal = $json->total;
	$currentNewsItems = 0;
	$newsCount = 0;
	$currentTime = time();
	$fourWeeksAgo = $currentTime - (60 * 60 * 24 * 7 * 4);
	
	// Function to write json for each news item
	function outputNews() {
		global $currentNewsItems, $newsCount, $fourWeeksAgo, $json;
		for ($i = 0; $i < $currentNewsItems; $i++) {
			if (strtotime($json->data[$i]->releaseDate) >= $fourWeeksAgo) {
				$newsCount = $newsCount + 1;
				echo '{';
				echo '"uid": "', $json->data[$i]->id, '",';
				echo '"updateDate": "', gmdate("Y-m-d\TH:i:s\Z", strtotime($json->data[$i]->releaseDate)), '",';
				echo '"titleText": ', json_encode($json->data[$i]->title), ',';
				if ($json->data[$i]->url != '') {
					echo '"mainText": ', json_encode($json->data[$i]->abstract), ',';
					echo '"redirectionUrl": "', $json->data[$i]->url, '"';
				} else {
					echo '"mainText": ', json_encode($json->data[$i]->abstract);
				}
				if ($currentNewsItems > $newsCount) {
					echo '},';
				} else {
					echo '}';
				}
			}
		}
	}
	
	for ($i = 0; $i < count($json->data); $i++) {
		if (strtotime($json->data[$i]->releaseDate) >= $fourWeeksAgo) {
			$currentNewsItems = $currentNewsItems + 1;
		}
	}
	
	if ($currentNewsItems > 0) {
		
		// Start outputting json if there are news items
		header('Content-Type: application/json');
		echo '[';
		
		// Output news items
		outputNews();
		
		echo ']';
		
	} else {
		
		// Start outputting json if there are no news items
		header('Content-Type: application/json');
		echo '[';
		echo '{';
		echo '"uid": "', uniqid('',true), '",';
		echo '"updateDate": "', $isoDate, '",';
		echo '"titleText": "No news.",';
		echo '"mainText": "There are no recent news items at this time. Please visit the park website for information about news and events.",';
		echo '"redirectionUrl": "https://www.nps.gov/', $park, '/news.htm"';
		echo '}';
		echo ']';
		
	}
?>