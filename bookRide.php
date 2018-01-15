<?php
include 'config/dbCon.php';
include_once 'utils/utils.php';
include_once 'utils/apiConstants.php';
include_once 'push/send_message.php';
include_once 'push/iPhonePush.php';
if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );
	bookRides ( $params );
}
function bookRides($params) {
	if (isset ( $params->rideId ) && isset ( $params->riderId ) && isset ( $params->seatsRequested )) {
		$rideId = $params->rideId;
		$userId = $params->riderId;
		$seatsRequested = $params->seatsRequested;
		if (checkAvailableSeats ( $rideId, $seatsRequested )) {
			$mysqli = getMysqli ();
			$query = "INSERT INTO bookings values(NULL,'$rideId',$seatsRequested,'Created','$userId')";
			$mysqli->query ( $query );
			if ($mysqli->insert_id > 0) {
				$response = array (
						"message" => "Booking Created" 
				);
				
				sendResponse ( $response );
				
				$userQuery  ="SELECT userName from users where user_id = $userId";
				$result = mysql_query($userQuery);
				while ( $row = mysql_fetch_array ( $result ) ) {
					$userName = $row['userName'];
				}
				
				$sourceQuery = "SELECT ridesavailable.userId as driverId,ridesavailable.id,users.user_id,users.userName,users.userPhoneNumber,users.deviceId,users.deviceType from ridesavailable,users where ridesavailable.id = $rideId and ridesavailable.userId = users.user_id";
				$res = mysql_query ( $sourceQuery );
				if (mysql_num_rows ( $res ) > 0) {
					$i = 0;
					while ( $row = mysql_fetch_array ( $res ) ) {
						$deviceId = $row['deviceId'];
						$deviceType = $row['deviceType'];
						$message = array("userId"=>$userId,
										 "rideId"=>$row['id'],
										 "seatsRequested"=>$seatsRequested,
										 "userName"=>$userName,
										 "type"=>"Request"
						);
						$msg = " has request a ride.";
						
						if($deviceType == "ios" ){
						sendIOSPush($deviceId, json_encode($message),$msg);
						}else{
						sendPushNotifictaion($deviceId, json_encode($message),$msg);
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

?>