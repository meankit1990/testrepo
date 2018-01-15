<?php
include_once 'config/dbCon.php';
include_once 'utils/utils.php';
include_once 'utils/queryUtils.php';
include_once 'utils/apiConstants.php';
include_once 'models/nearByRidesModel.php';
if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );
	
	if (isset ( $params->latFrom ) && isset ( $params->longFrom )) {
		$out = getNearByRides ( $params );
	}
	
	if (isset ( $params->userId )) {
		$userId = $params->userId;
		$prefRides = getPreferedRides ( $userId );
	}
	
	$popularRoutes = getPopularRide ();
	
	//$final = array ();
	$result = [];
	
	if (isset ( $out )) {
		$resultJson1 = [ 
				'nearByRides' => $out 
		];
		//array_push ( $final, $resultJson );
		$result = $resultJson1;
	}
	if (isset ( $prefRides )) {
		$resultJson2 = [ 
				'preferedRoutes' => $prefRides 
		];
		//array_push ( $final, $resultJson );
		$result = array_merge( $result, $resultJson2);
	}
	if (isset ( $popularRoutes )) {
		$resultJson3 = [ 
				'popularRides' => $popularRoutes 
		];
		//array_push ( $final, $resultJson );
		$result = array_merge( $result, $resultJson3);

	}
	
	if(count($result)>0)
		sendResponse ( $result, false );
	else
		sendNoDataFound();
}
function getNearByRides($params) {
	if (isset ( $params->latFrom ) && isset ( $params->longFrom )) {
		$latFrom = $params->latFrom;
		$longFrom = $params->longFrom;
		$convertedTime = date ( 'Y-m-d H:i:s', time () );
		$routeQuery = "SELECT COUNT(*) As totalRides,routeId,routeAddressLine1,routeAddressLine2,rides.* FROM routes,ridesAvailable as rides WHERE routeId IN (SELECT routeId FROM `routes` as s WHERE (ACOS( SIN( RADIANS (s.routeLatitude) ) * SIN( RADIANS( $latFrom ) ) + COS( RADIANS ( s.routeLatitude ) )* COS( RADIANS( $latFrom )) * COS( RADIANS (s.routeLongitude) - RADIANS( $longFrom )) ) * 6380 < 1)) AND routeType='destination' AND routes.routeId = rides.id AND (TIMESTAMPDIFF(DAY, cast('$convertedTime' As Date),cast(rideTimeStamp As Date)) = 0) AND (TIMESTAMPDIFF(MINUTE,cast('$convertedTime' As Time),cast(rideTimeStamp As Time)) > 0) GROUP BY routeLatitude,routeLongitude";
		$res = mysql_query ( $routeQuery );
		$i = 0;
		if (mysql_num_rows ( $res ) > 0) {
			$ridesArray = array ();
			while ( $row = mysql_fetch_array ( $res ) ) {
				$myArray = array ();
				$myArray [$i] = new NearByRide ();
				$sourcePoint = getSourceDetails ( $row [ROUTE_ID] );
				$destinationPoint = getDestinationDetails ( $row [ROUTE_ID] );
				$myArray [$i]->setSourceAddress ( $sourcePoint );
				$myArray [$i]->setDestinationAddress ( $destinationPoint );
				$myArray [$i]->setTotalCount ( $row ['totalRides'] );
				array_push ( $ridesArray, $myArray [$i]->toJSON () );
			}
			$encodedJson = json_encode ( $ridesArray );
			$out = json_decode ( $encodedJson );
		} 
		return $out;
	} else {
		sendInvalidParameterResponse ();
	}
}
function getPreferedRides($userId) {
	$convertedTime = date ( 'Y-m-d H:i:s', time () );
	$routeQuery = "SELECT * from " . PREFERED_ROUTE_TABLE . " WHERE userId='$userId'";
	$res = mysql_query ( $routeQuery );
	$i = 0;
	if (mysql_num_rows ( $res ) > 0) {
		$ridesArray = array ();
		while ( $row = mysql_fetch_array ( $res ) ) {
			
			$routeArray = [ 
					"source" => [ 
							ROUTE_ADDRESS1 => $row ['sourceAddressLine1'],
							ROUTE_ADDRESS2 => $row ['sourceAddressLine2'],
							ROUTE_LAT => $row ['sourceLatitude'],
							ROUTE_LONG => $row ['sourceLongitude'] 
					],
					"destination" => [ 
							ROUTE_ADDRESS1 => $row ['destinationAddressLine1'],
							ROUTE_ADDRESS2 => $row ['destinationAddressLine2'],
							ROUTE_LAT => $row ['destinationLatitude'],
							ROUTE_LONG => $row ['destinationLongitude'] 
					] 
			];
			array_push ( $ridesArray, $routeArray );
		}
		return $ridesArray;
	}
}
function getPopularRide() {
	$routeQuery = "Select *,count(*) from routes where routeAddressLine1 like (Select routeAddressLine1 from routes
 				   group by routeAddressLine1 
 				   order by count(*) desc limit 1)limit 10";
	$res = mysql_query ( $routeQuery );
	$i = 0;
	if (mysql_num_rows ( $res ) > 0) {
		$ridesArray = array ();
		while ( $row = mysql_fetch_array ( $res ) ) {
			
			$routeQuery2 = "Select * from routes where routeAddressLine1 = '$row[routeAddressLine1]' group by routeAddressLine2";
			$res2 = mysql_query ( $routeQuery );
			if (mysql_num_rows ( $res2 ) > 0) {
				$ridesArray2 = array ();
				while ( $row2 = mysql_fetch_array ( $res2 ) ) {
					$routeArray = [ 
							"source" => [ 
									ROUTE_ADDRESS1 => $row2 ['routeAddressLine1'],
									ROUTE_ADDRESS2 => $row2 ['routeAddressLine2'],
									ROUTE_LAT => $row2 ['routeLatitude'],
									ROUTE_LONG => $row2 ['routeLongitude'] 
							],
							"destination" => [ 
									ROUTE_ADDRESS1 => $row2 ['routeAddressLine1'],
									ROUTE_ADDRESS2 => $row2 ['routeAddressLine2'],
									ROUTE_LAT => $row2 ['routeLatitude'],
									ROUTE_LONG => $row2 ['routeLongitude'] 
							] 
					];
					array_push ( $ridesArray, $routeArray );
				}
			}
			return $ridesArray;
		}
	}
}
?>