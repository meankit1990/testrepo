<?php
include 'config/dbCon.php';
include_once 'utils/utils.php';
include_once 'utils/apiConstants.php';

if(!isset($_GET['operation'])){
	sendInvalidParameterResponse();
}else{
	$operation = $_GET['operation'];
}

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );
	operation($params);
}

function bookRide($params)
{
	if(isset($params->rideId) && isset($params->seatsRequired) && isset($params->userId)){
		$rideId=  $params->rideId;
		$seatsRequested = $params->seatsRequired;
		$userId = $params->userId;
		
		$seatsAvailable = getAvailableSeats($rideId);
		
		$mysqli = getMysqli ();
		$query = "UPDATE " . RIDE_AVAILABLE_TABLE . " SET rideStatus = 'Start'";
		$mysqli->query ( $query );
		
		if($mysqli->affected_rows > 0){
			$response = array("message"=>"Ride Started");
		}else{
			sendUnableToSave();
		}
	}else{
		sendInvalidParameterResponse();
	}
}

function endRide($params)
{
	if(isset($params->rideId)){
		$rideId=  $params->rideId;
		$mysqli = getMysqli ();
		$query = "UPDATE " . RIDE_AVAILABLE_TABLE . " SET rideStatus = 'End'";
		$mysqli->query ( $query );

		if($mysqli->affected_rows > 0){
			$response = array("message"=>"Ride Ended");
		}else{
			sendUnableToSave();
		}
	}else{
		sendInvalidParameterResponse();
	}
}
function getAvailableSeats($rideId){
	$query = "SELECT seatsOffered from " . RIDE_AVAILABLE_TABLE . " WHERE id = $rideId";
	$result = mysql_query($query);
	
}
?>