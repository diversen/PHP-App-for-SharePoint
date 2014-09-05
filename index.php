<?php
 // Include the TokenHelper class
 require_once 'TokenHelper.php';

 // The PHP web application should be started with a SharePoint context, which uses a POST request to send token information
 // Make sure that you browse to the contents page of the SharePoint site where you deployed the app.
 if($_SERVER['REQUEST_METHOD'] == 'GET'){
 	echo 'You must launch this web app from the contents page of the SharePoint site where you deployed the app for SharePoint.';
 	exit;
 }
 // When the web application is launched from the SharePoint contents page, it sends SPSiteUrl and SPAppToken parameters
 //	in the body of the request. 
 if($_SERVER['REQUEST_METHOD'] == 'POST'){
 	// Initialize the TokenHelper class with parameters from the POST request
 	$tokenHelper = new TokenHelper($_POST['SPSiteUrl'], $_POST['SPAppToken']);
 	// Get the access token object from the TokenHelper class
 	$accessToken = $tokenHelper->GetAccessToken();
 	
 	//Initialize a CURL instance
 	$ch = curl_init();
 	// //This is the REST endpoint that we are sending our request to
 	$apiUrl = $_POST['SPSiteUrl'] . '/_api/web/title';
 	curl_setopt($ch, CURLOPT_URL, $apiUrl);
 	// Indicate that we want the response back as a string
 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	// FIXME: By default, HTTPS does not work with curl. This is a workaround for developer environments.
	// Using this CURL option exposes the PHP server to man-in-the-middle attacks. Remove in production scenarios.
 	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 	//Add accept and authorization headers, along with the access token
 	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$accessToken->access_token, 'Accept:application/json;odata=verbose'));
 	
 	// Execute the request and get the response in the output variable as a string
 	$output = curl_exec($ch);
 	// Close curl instance to free up system resources
 	curl_close($ch);
 	// Decode the response using the JSON decoder
 	$jsonResult = json_decode($output);
 	// Print the result to the page
 	echo 'Querying the REST endpoint '. $apiUrl . '<br/>';
 	echo 'Result: ' . $jsonResult->d->Title;
 }
?>
