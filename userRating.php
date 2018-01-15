<?php
include 'config/dbCon.php';
include_once 'utils/utils.php';
include_once 'utils/apiConstants.php';


if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );	
	userRating($params);
}

function userRating($params){
	if(isset($params->userId) && isset($params->rideId) && isset($params->rating)){
		$userId = $params->userId;
		$rideId = $params->rideId;
		$rating = $params->rating;
		
		$mysqli = getMysqli ();
		$query = "INSERT INTO " . USER_RATING_TABLE . " VALUES
		(NULL, '$userId','$rideId',$rating)";
		$mysqli->query ( $query );
		
		$rideId = $mysqli->insert_id;
		if($rideId > 0){
			$response = array("message"=>"User Rating Successfully Saved");
			sendResponse($response);
		}else{
			sendUnableToSave();
		}
	}else{
		sendInvalidParameterResponse();
	}
}
?>