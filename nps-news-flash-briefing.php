<?php
	// api access point
	$dataURL = "https://developer.nps.gov/api/v0/newsreleases?limit=5";
	
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
	$newsCount = 0;
	
	// Function to write json for each news item
	function outputNews() {
		global $newsCount, $json;
		for ($i = 0; $i < 5; $i++) {
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
			if (5 > $newsCount) {
				echo '},';
			} else {
				echo '}';
			}
		}
	}
	
	if ($newsTotal > 0) {
		
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
		echo '"mainText": "There are no news items at this time. Please visit the website for information about news and events.",';
		echo '"redirectionUrl": "https://www.nps.gov/aboutus/news/news-releases.htm"';
		echo '}';
		echo ']';
		
	}
?>