<?php
include 'config/dbCon.php';
include_once 'utils/errorUtils.php';
include_once 'models/searchRideModel.php';
include_once 'utils/queryUtils.php';
include_once 'utils/apiConstants.php';
include_once 'utils/sortFilterUtils.php';
// include 'logger.php';
date_default_timezone_set ( 'Asia/Kolkata' );

$urlParams = explode ( '/', $_SERVER ['REQUEST_URI'] );

$functionName = $urlParams [3];
if (function_exists ( $functionName )) {
	$functionName ();
} else {
	$errorMessage = new Error ();
	$errorMessage->setErrorId ( "400" );
	$errorMessage->setErrorMessage ( "Bad Request!!! Kindly Check URL" );
	$out = $errorMessage->toJSON ();
	echo json_encode ( $out );
}

function getRides() {
	if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
		$params = json_decode ( file_get_contents ( "php://input" ) );
		$latFrom = $params->latFrom;
		$longFrom = $params->longFrom;
		$latTo = $params->latTo;
		$longTo = $params->longTo;
		$timeStamp = $params->rideTime;
		$sortBy = $params->sortBy;
		$sourceRadius = 1;
		$destinationRadius = 1;
		
		if(isset($params->sourceRadius)){
			$sourceRadius = $params->sourceRadius;
		}
		
		if(isset($params->destinationRadius)){
			$destinationRadius = $params->destinationRadius;
		}

		$convertedTime = date ( 'Y-m-d H:i:s', $timeStamp );
		
		$ridesQuery = "SELECT Rides.* FROM " . RIDE_AVAILABLE_TABLE . " AS Rides WHERE
						((ACOS( SIN( RADIANS(" . RIDE_SOURCE_LAT . ") ) * SIN( RADIANS( $latFrom ) ) + COS( RADIANS( " . RIDE_SOURCE_LAT . " ) )
						* COS( RADIANS( $latFrom )) * COS( RADIANS( " . RIDE_SOURCE_LONG . " ) - RADIANS( $longFrom )) ) * 6380 < $sourceRadius) and
						(ACOS( SIN( RADIANS( " . RIDE_DESTINATION_LAT . ") ) * SIN( RADIANS( $latTo ) ) + COS( RADIANS( " . RIDE_DESTINATION_LAT . " ) )
						* COS( RADIANS( $latTo )) * COS( RADIANS( " . RIDE_DESTINATION_LONG . " ) - RADIANS( $longTo )) ) * 6380 < $destinationRadius)) 
						and (TIMESTAMPDIFF(DAY, cast('$convertedTime' As Date),cast(rideTimeStamp As Date)) = 0)
						and (TIMESTAMPDIFF(MINUTE,cast('$convertedTime' As Time),cast(rideTimeStamp As Time)) > 0) ORDER BY ID";
		
		$res = mysql_query ( $ridesQuery );
		$i = 0;
		$ridesArray = array ();
		if (mysql_num_rows ( $res ) > 0) {
			while ( $row = mysql_fetch_array ( $res ) ) {
				$myArray = array ();
				$myArray [$i] = new SearchRide ();
				
				// Set Ride Data
				$myArray [$i]->setRideId ( $row [RIDE_ID] );
				$myArray [$i]->setSourceAddress ( $row [RIDE_SOURCE_ADDRESS], $row [RIDE_SOURCE_LAT], $row [RIDE_SOURCE_LONG] );
				$myArray [$i]->setDestinationAddress ( $row [RIDE_DESTINATION_ADDRESS], $row [RIDE_DESTINATION_LAT], $row [RIDE_DESTINATION_LONG] );
				$myArray [$i]->setRidePreferences ( $row [RIDE_SMOKING_ALLOWED], $row [RIDE_BAGGAGE_ALLOWED], $row [RIDE_ANIMAL_ALLOWED], $row [RIDE_DRINK_ALLOWED] );
				$myArray [$i]->setRideTimeStamp ( $row [RIDE_TIME_STAMP] );
				$myArray [$i]->setSeatsOffered ( $row [RIDE_SEATS_OFFERED] );
				$myArray [$i]->setIsReturnTrip ( $row [RIDE_ROUND_TRIP] );
				$myArray [$i]->setReturnTimeStamp ( $row [RIDE_RETURN_TIME_STAMP] );
				$myArray [$i]->setRideRemarks ( $row [RIDE_REMARKS] );
				
				// set Driver Data
				$userModel = getUserDetails ( $row [USER_ID] );
				$myArray [$i]->setUserData ( $userModel );
				
				// set Car Data
				$carModel = getCarDetails ( $row [CAR_ID] );
				$myArray [$i]->setCarData ( $carModel );
				
				// set Route Data
				$routeModel = getRoutesDetails ( $row [ID] );
				$myArray [$i]->setRouteData ( $routeModel );
				
				array_push ( $ridesArray, $myArray [$i]->toJSON () );
			}
			$encodedJson = json_encode ( $ridesArray );
			$out = json_decode ( $encodedJson );
			
		switch ($sortBy) {
			
			case 'userRatingASC' :
				usort ( $out, 'sortUserAsc');	
			break;
			
			case 'userRatingDESC' :
				usort ( $out, 'sortUserDesc');
				break;
			
			case 'timeASC' :
			     usort ( $out, 'sortTimeAsc');
				 break;
				 
			case 'timeDESC' :
			 	 usort ( $out, 'sortTimeDesc');
				 break;
				 
			default: 
				usort ( $out, 'sortDefault');
				break;
		}
			
		} else {
			$errorMessage = new Error ();
			$errorMessage->setErrorId ( "204" );
			$errorMessage->setErrorMessage ( "No Rides for the route" );
			$out = $errorMessage->toJSON ();
		}
		echo json_encode ( $out );
	}
}
?>