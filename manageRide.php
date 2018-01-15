<?php
include 'config/dbCon.php';
include_once 'utils/utils.php';
include_once 'utils/apiConstants.php';
include_once 'push/iPhonePush.php';
include_once 'push/send_message.php';

if (! isset ( $_GET ['operation'] )) {
	sendInvalidParameterResponse ();
} else {
	$operation = $_GET ['operation'];
}

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );
	$operation ( $params );
}
function acceptRide($params) {
	if (isset ( $params->requestId )) {
		$rideRequestRide = $params->requestId;
		$rideId = $params->rideId;
		$seatsRequested = $params->seatsRequested;
		
		$mysqli = getMysqli ();
		if (checkAvailableSeats ( $rideId, $seatsRequested )) {
			$query = "UPDATE " . BOOKING_TABLE . " SET bookingStatus = 'Accepted' WHERE id = '$rideRequestRide'";
			$mysqli->query ( $query );
			if ($mysqli->affected_rows > 0) {
				$response = array (
						"message" => "Ride Accepted" 
				);
				sendResponse ( $response );
				
				$userQuery = "SELECT users.userName,ridesavailable.userId from ridesavailable,users where ridesavailable.id = $rideId and ridesavailable.userId = users.user_id";
				$result = mysql_query ( $userQuery );
				while ( $row = mysql_fetch_array ( $result ) ) {
					$userName = $row ['userName'];
					$userId = $row ['userName'];
				}
				
				$sourceQuery = "SELECT bookings.userId as driverId,bookings.id,users.user_id,users.userPhoneNumber,users.deviceId,users.deviceType from bookings,users where bookings.id = $rideRequestRide and bookings.userId = users.user_id";
				$res = mysql_query ( $sourceQuery );
				if (mysql_num_rows ( $res ) > 0) {
					$i = 0;
					while ( $row = mysql_fetch_array ( $res ) ) {
						$deviceId = $row ['deviceId'];
						$deviceType = $row ['deviceType'];
						$message = array (
								"userId" => $userId,
								"rideId" => $rideId,
								"seatsRequested" => $seatsRequested,
								"userName" => $userName,
								"type" => "Response" 
						);
						$msg = "accepted your ride";
						if ($deviceType == "ios") {
							sendIOSPush ( $deviceId, json_encode ( $message ), $msg );
						} else {
							sendPushNotifictaion ( $deviceId, json_encode ( $message ), $msg );
						}
					}
				}
			} else {
				sendUnableToSave ();
			}
		} else {
			sendNoSeatsAvailable ();
		}
	} else {
		sendInvalidParameterResponse ();
	}
}
function rejectRide($params) {
	if (isset ( $params->rideId ) && isset ( $params->riderId )) {
		$rideId = $params->rideId;
		$userId = $params->riderId;
		$mysqli = getMysqli ();
		$query = "UPDATE " . BOOKING_TABLE . " SET bookingStatus = 'Rejected' WHERE rideId = '$rideId' and userId = '$userId'";
		$mysqli->query ( $query );
		
		if ($mysqli->affected_rows > 0) {
			$response = array (
					"message" => "Ride Rejected" 
			);
			sendResponse ( $response );
			
			$userQuery = "SELECT users.userName,ridesavailable.userId from ridesavailable,users where ridesavailable.id = $rideId and ridesavailable.userId = users.user_id";
			$result = mysql_query ( $userQuery );
			while ( $row = mysql_fetch_array ( $result ) ) {
				$userName = $row ['userName'];
				$userId = $row ['userName'];
			}
			$sourceQuery = "SELECT bookings.userId as driverId,bookings.id,users.user_id,users.userPhoneNumber,users.deviceId,users.deviceType from bookings,users where bookings.rideId = $rideId and bookings.userId = users.user_id";
			$res = mysql_query ( $sourceQuery );
			if (mysql_num_rows ( $res ) > 0) {
				$i = 0;
				while ( $row = mysql_fetch_array ( $res ) ) {
					$deviceId = $row ['deviceId'];
					$deviceType = $row ['deviceType'];
					$message = array (
							"userId" => $userId,
							"rideId" => $rideId,
							"userName" => $userName,
							"type" => "Response" 
					);
					$msg = "rejected your ride";
					if ($deviceType == "ios") {
						sendIOSPush ( $deviceId, json_encode ( $message ), $msg );
					} else {
						sendPushNotifictaion ( $deviceId, json_encode ( $message ), $msg );
					}
				}
			}
		} else {
			sendUnableToSave ();
		}
	} else {
		sendInvalidParameterResponse ();
	}
}
function cancelRide($params) {
	if (isset ( $params->rideId )) {
		$rideId = $params->rideId;
		$mysqli = getMysqli ();
		$query = "UPDATE " . BOOKING_TABLE . " SET bookingStatus = 'Cancelled' WHERE rideId = '$rideId'";
		$mysqli->query ( $query );
		
		$query = "UPDATE " . RIDE_AVAILABLE_TABLE . " SET rideStatus = 'Cancelled' WHERE rideId = '$rideId'";
		$mysqli->query ( $query );
		
		if ($mysqli->affected_rows > 0) {
			$response = array (
					"message" => "Ride Cancelled" 
			);
			sendResponse ( $response );
			
			$userQuery = "SELECT users.userName,ridesavailable.userId from ridesavailable,users where ridesavailable.id = $rideId and ridesavailable.userId = users.user_id";
			$result = mysql_query ( $userQuery );
			while ( $row = mysql_fetch_array ( $result ) ) {
				$userName = $row ['userName'];
				$userId = $row ['userName'];
			}
			$sourceQuery = "SELECT bookings.userId as driverId,bookings.id,users.user_id,users.userPhoneNumber,users.deviceId,users.deviceType from bookings,users where bookings.rideId = $rideId";
			$res = mysql_query ( $sourceQuery );
			if (mysql_num_rows ( $res ) > 0) {
				$i = 0;
				while ( $row = mysql_fetch_array ( $res ) ) {
					$deviceId = $row ['deviceId'];
					$deviceType = $row ['deviceType'];
					$message = array (
							"userId" => $userId,
							"rideId" => $rideId,
							"userName" => $userName,
							"type" => "Response" 
					);
					$msg = " cancelled the ride";
					if ($deviceType == "ios") {
						sendIOSPush ( $deviceId, json_encode ( $message ), $msg );
					} else {
						sendPushNotifictaion ( $deviceId, json_encode ( $message ), $msg );
					}
				}
			}
		} else {
			sendUnableToSave ();
		}
	} else {
		sendInvalidParameterResponse ();
	}
}
function cancelRequest($params) {
	if (isset ( $params->requestId ) && isset ( $params->userId )) {
		$requestId = $params->requestId;
		$userId = $params->userId;
		$mysqli = getMysqli ();
		$query = "UPDATE " . BOOKING_TABLE . " SET bookingStatus = 'Cancelled' WHERE id = $requestId and userId = '$userId'";
		$mysqli->query ( $query );
		if ($mysqli->affected_rows > 0) {
			$response = array (
					"message" => "Ride Cancelled" 
			);
			sendResponse ( $response );
			
			$userQuery = "SELECT userName from users where user_id = $userId";
			$result = mysql_query ( $userQuery );
			while ( $row = mysql_fetch_array ( $result ) ) {
				$userName = $row ['userName'];
			}
			
			$sourceQuery = "SELECT ridesavailable.userId as driverId,ridesavailable.id,users.user_id,users.userName,users.userPhoneNumber,users.deviceId,users.deviceType from ridesavailable,users where ridesavailable.id = $rideId and ridesavailable.userId = users.user_id";
			$res = mysql_query ( $sourceQuery );
			if (mysql_num_rows ( $res ) > 0) {
				$i = 0;
				while ( $row = mysql_fetch_array ( $res ) ) {
					$deviceId = $row ['deviceId'];
					$deviceType = $row ['deviceType'];
					$message = array (
							"userId" => $userId,
							"rideId" => $row ['id'],
							"userName" => $userName,
							"type" => "Request" 
					);
					$msg = " cancelled his ride request";
					
					if ($deviceType == "ios") {
						sendIOSPush ( $deviceId, json_encode ( $message ) );
					} else {
						sendPushNotifictaion ( $deviceId, json_encode ( $message ) );
					}
				}
			}
		} else {
			sendUnableToSave ();
		}
	} else {
		sendInvalidParameterResponse ();
	}
}
?>