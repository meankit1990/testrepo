<?php
include 'config/dbCon.php';
include_once 'utils/queryUtils.php';
include_once 'utils/utils.php';
include_once 'models/RiderBookingModel.php';
if ($_SERVER ['REQUEST_METHOD'] == 'GET') {
	if (isset ( $_GET ['userId'] )) {
		getBookingList ( $_GET ['userId'] );
	} else {
		sendInvalidParameterResponse ();
	}
}
function getBookingList($userId) {
	$bookingListQuery = "SELECT * from bookings where userId = '$userId'";
	$res = mysql_query ( $bookingListQuery );
	$ridesArray = array ();
	if (mysql_num_rows ( $res ) > 0) {
		$i = 0;
		$query = "SELECT DISTINCT bookings.*,bookings.id as requestId,ridesavailable.*,ridesavailable.id as ride_id, users.*,users.user_id as driverId,routes.routeId as routeId,cars.*,cars.id as carId  FROM bookings,ridesavailable,users,routes,cars WHERE bookings.userId = '$userId' and rideId = ridesavailable.id and users.user_id = ridesavailable.userId and routes.routeId = ridesavailable.id and cars.id = ridesavailable.carId";
		$result = mysql_query ( $query );
		while ( $rows = mysql_fetch_array ( $result ) ) {
			$myArray = array ();
			$myArray [$i] = new RiderBookingModel();
			$routePoints = getRoutesDetails ( $rows ['routeId'] );
			$sourcePoint = getSourceDetails ( $rows ['routeId'] );
			$destinationPoint = getDestinationDetails ( $rows ['routeId'] );
		
			$myArray [$i]->setRouteData ( $routePoints );
			$myArray [$i]->setSource ( $sourcePoint );
			$myArray [$i]->setDestination ( $destinationPoint );
				
			$myArray [$i]->setRideId ( $rows ['ride_id'] );
			$myArray [$i]->setRideTimeStamp ( strtotime ( $rows['rideTimeStamp'] ) );
			$myArray [$i]->setRequestId ( $rows ['requestId'] );
			
			$myArray [$i]->setRidePreferences ( $rows [RIDE_SMOKING_ALLOWED], $rows [RIDE_BAGGAGE_ALLOWED], $rows [RIDE_ANIMAL_ALLOWED], $rows [RIDE_DRINK_ALLOWED] );
			$myArray [$i]->setSeatsOffered ( $rows [RIDE_SEATS_OFFERED] );
			$myArray [$i]->setSeatsRequested($rows ['seatsRequested']);
			
			$myArray [$i]->setIsReturnTrip ( $rows [RIDE_ROUND_TRIP] );
			$myArray [$i]->setReturnTimeStamp ( strtotime ( $rows [RIDE_RETURN_TIME_STAMP] ) );
			$myArray [$i]->setRideRemarks ( $rows [RIDE_REMARKS] );
			$myArray [$i]->setRideStatus ($rows['bookingStatus']);
			$userModel = getUserDetails ( $rows );
			$myArray [$i]->setDriverDetails ( $userModel );
				
			// set Car Data
			$carModel = getCarDetails ( $rows );
			$myArray [$i]->setCarData ( $carModel );
			
			// set Car Data
			array_push ( $ridesArray, $myArray [$i]->toJSON () );
		}
		$rideArrays = array("nextUrl"=>'',
							"data"=>$ridesArray);
		echo json_encode($rideArrays);
	} else {
		sendNoDataFound ();
	}
}
?>