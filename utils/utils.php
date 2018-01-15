<?php
require 'models/baseApiResponse.php';
require 'errorUtils.php';
function sendResponse($data) {
	echo json_encode ( $data );
}
function sendInvalidParameterResponse() {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "403" );
	$errorMessage->setErrorMessage ( "Request Parameters are not set" );
	$out = $errorMessage->toJSON ();
	echo json_encode ( $out );
}
function sendNoDataFound() {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "400" );
	$errorMessage->setErrorMessage ( "No Data Found" );
	$out = $errorMessage->toJSON ();
	sendResponse ( $out, false );
}
function sendbadRequestResult() {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "403" );
	$errorMessage->setErrorMessage ( "Bad Request!!!" );
	$out = $errorMessage->toJSON ();
	sendResponse ( $out, false );
}
function sendUnableToSave() {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "401" );
	$errorMessage->setErrorMessage ( "Unable to save Data" );
	$out = $errorMessage->toJSON ();
	sendResponse ( $out, false );
}
function sendNoChanges() {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "401" );
	$errorMessage->setErrorMessage ( "No Changes in User Info" );
	$out = $errorMessage->toJSON ();
	sendResponse ( $out, false );
}
function sendNoSeatsAvailable() {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "401" );
	$errorMessage->setErrorMessage ( "No Seats Availble for this ride" );
	$out = $errorMessage->toJSON ();
	sendResponse ( $out, false );
}
function checkAvailableSeats($rideId, $seatsRequested) {
	$seatsOffered = 0;
	$seatsRequest = 0;
	$routeQuery = "SELECT seatsOffered from " . RIDE_AVAILABLE_TABLE . " WHERE id = $rideId";
	$res = mysql_query ( $routeQuery );
	if (mysql_num_rows ( $res ) > 0) {
		while ( $row = mysql_fetch_array ( $res ) ) {
			$seatsOffered = $row ['seatsOffered'];
		}
	}
	
	$routeQuery = "SELECT seatsRequested from " . BOOKING_TABLE . " WHERE rideId = '$rideId' AND bookingStatus = 'Accepted'";
	$res = mysql_query ( $routeQuery );
	if (mysql_num_rows ( $res ) > 0) {
		while ( $row = mysql_fetch_array ( $res ) ) {
			$seatsRequest += $row ['seatsRequested'];
		}
	}
	if ($seatsOffered >= ($seatsRequest + $seatsRequested)) {
		return true;
	} else {
		return false;
	}
}
function getBooleanValue($value){
	$resultValue;
	switch ($value){
		case 0:
			$resultValue= false;
			break;
		case 1:
			$resultValue =true;
			break;
		default:$resultValue = null;

	}
	return $resultValue;
}
function getDeviceType(){
	if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
		return "ios";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
		return "ios";
	} else if(stristr($_SERVER['HTTP_USER_AGENT'],'os') || strstr($_SERVER['HTTP_USER_AGENT'],'os'))
	{
		return "ios";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
		return "android";
	}else{
		return "other";
	}
}
?>
