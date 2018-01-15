<?php
include 'config/dbCon.php';
include_once 'utils/utils.php';
include_once 'models/searchRideModel.php';
include_once 'utils/queryUtils.php';
include_once 'utils/apiConstants.php';
include_once 'utils/UploadToServer.php';
include_once 'models/RouteModel.php';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );
	$rideId = insertRideData ( $params );
	if (isset ( $rideId ) && $rideId > 0) {
		$out = [ 
				"message" => "Ride Successfully added",
				"rideId" => $rideId 
		];
		sendResponse ( $out, false );
	} else {
		$errorMessage = new Error ();
		$errorMessage->setErrorId ( "400" );
		$errorMessage->setErrorMessage ( "Unable to add ride" );
		$json = $errorMessage->toJSON ();
		sendResponse ( $json ,false);
	}
}
function insertRideData($params) {
	if (isset ( $params->userId ) && isset ( $params->isReturntrip ) && isset ( $params->rideTimeStamp ) && isset ( $params->seatsOffered ) && isset ( $params->returnTimeStamp ) && isset ( $params->source ) && isset ( $params->destination ) && isset ( $params->isPreferedRoute ))
	{
	$userId = $params->userId;
	$isRoundTrip = $params->isReturntrip;
	$rideTimeStamp = $params->rideTimeStamp;
	$seatsOffered = $params->seatsOffered;
	$returnTimeStamp = $params->returnTimeStamp;
	$convertedTime = date ( 'Y-m-d H:i:s', $rideTimeStamp/1000 );
	
	$sourceAddress = $params->source;
	$destinationAddress = $params->destination;
	$isPreferedRoute = $params->isPreferedRoute;
	
	if ($returnTimeStamp == null) {
		$convertedReturnTimeStamp = "NULL";
	} else {
		$convertedReturnTimeStamp = date ( 'Y-m-d H:i:s', $returnTimeStamp/1000 );
	}
	$ridePreferences = $params->ridePreferences;
	$isSmokingAllowed = $ridePreferences->isSmokingAllowed;
	$isBaggageAllowed = $ridePreferences->isBaggageAllowed;
	$isAnimalAllowed = $ridePreferences->isAnimalsAllowed;
	$isFoodDrinkAllowed = $ridePreferences->isFoodDrinkAllowed;
	$routePoints = $params->routePoints;
	$carId = $params->carId;
	$mysqli = getMysqli ();
	$query = "INSERT INTO " . RIDE_AVAILABLE_TABLE . "(id,userId,rideTimeStamp,seatsOffered,isSmokingAllowed,isBaggageAllowed,isAnimalsAllowed,isFoodDrinkAllowed,isReturnTrip,returnTimeStamp,carId,rideStatus) VALUES
	(NULL, $userId,'$convertedTime',$seatsOffered,'$isSmokingAllowed','$isBaggageAllowed','$isAnimalAllowed','$isFoodDrinkAllowed','$isRoundTrip','$convertedReturnTimeStamp',$carId,'Created')";
	$mysqli->query ( $query );
	$rideId = $mysqli->insert_id;
	
	insertEndPoint ( $sourceAddress, $rideId, "source" );
	
	foreach ( $routePoints as $routePoint ) {
		$routeModel = new RouteModel ();
		$routeModel->setRouteAddressLine1 ( $routePoint->routeAddressLine1 );
		$routeModel->setRouteAddressLine2 ( $routePoint->routeAddressLine2 );
		$routeModel->setRouteLat ( $routePoint->routeLatitude );
		$routeModel->setRouteLong ( $routePoint->routeLongitude );
		$sql = "INSERT INTO " . ROUTE_TABLE . "(routeId,routeAddressLine1,routeAddressLine2,routeLatitude, routeLongitude,routeType) values('$rideId','$routePoint->routeAddressLine1','$routePoint->routeAddressLine2','$routePoint->routeLatitude','$routePoint->routeLongitude','via')";
		$mysqli->query ( $sql );
	}
	
	insertEndPoint ( $destinationAddress, $rideId, "destination" );
	$query = "INSERT INTO " . PREFERED_ROUTE_TABLE . " values(NULL,'$userId','$sourceAddress->routeAddressLine1','$sourceAddress->routeAddressLine2','$sourceAddress->routeLatitude','$sourceAddress->routeLongitude','$destinationAddress->routeAddressLine1','$destinationAddress->routeAddressLine2','$destinationAddress->routeLatitude','$destinationAddress->routeLongitude')";
	$mysqli->query ( $query );
	return $rideId;
	}else{
		sendInvalidParameterResponse();
	}
}
function insertEndPoint($sourceAddress, $rideId, $routeType) {
	$routeModel = new RouteModel ();
	$mysqli = getMysqli ();
	$routeModel->setRouteAddressLine1 ( $sourceAddress->routeAddressLine1 );
	$routeModel->setRouteAddressLine2 ( $sourceAddress->routeAddressLine2 );
	$routeModel->setRouteLat ( $sourceAddress->routeLatitude );
	$routeModel->setRouteLong ( $sourceAddress->routeLongitude );
	$routeModel->setRouteType ( SOURCE );
	$sql = "INSERT INTO " . ROUTE_TABLE . "(routeId,routeAddressLine1,routeAddressLine2,routeLatitude, routeLongitude,routeType) values('$rideId','$sourceAddress->routeAddressLine1','$sourceAddress->routeAddressLine2','$sourceAddress->routeLatitude','$sourceAddress->routeLongitude','$routeType')";
	$mysqli->query ( $sql );
}
?>