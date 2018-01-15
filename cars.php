<?php
include 'config/dbCon.php';
include_once 'models/CarModel.php';
include_once 'utils/queryUtils.php';
include_once 'utils/apiConstants.php';
include_once 'utils/UploadToServer.php';
include_once 'utils/utils.php';

if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
	$params = json_decode ( file_get_contents ( "php://input" ) );
	insertCarData ( $params );
} else if ($_SERVER ['REQUEST_METHOD'] == 'GET') {
	if(isset($_GET ['userId'])){
		$userId = $_GET ['userId'];
		getAllCars ( $userId );
	}else{
		sendInvalidParameterResponse();
	}
}
function insertCarData($params) {
	if (isset ( $params->userId ) && isset ( $params->licensePlateNumber ) && isset ( $params->company ) && isset ( $params->carModel ) && isset ( $params->carColor )) {
		$userId = $params->userId;
		$licenseNumber = $params->licensePlateNumber;
		$company = $params->company;
		$carModel = $params->carModel;
		$carColor = $params->carColor;
		
		if (isset ( $carImageData )) {
			$carImageData = $params->carImageData;
		} else {
			$carImageData = "default";
		}
		$mysqli = getMysqli ();
		$imageUrl = uploadToServer ( $userId, 'car', $carImageData );
		$query = "Insert Into " . CAR_TABLE . " values(NULL,'$userId','$licenseNumber','$company','$carModel','$carColor','$imageUrl')";
		$mysqli->query ( $query );
		$carId = $mysqli->insert_id;
		$out = [
				"message" => "Car Successfully added",
				"carId" => $carId
		];
		sendResponse($out);
	}else{
		sendInvalidParameterResponse();
	}
}
function getAllcars($userId) {
	$query = "SELECT * FROM " . CAR_TABLE . " WHERE userId = '$userId'";
	$res = mysql_query ( $query );
	$out = array ();
	if(mysql_num_rows($res) >0){
	while ( $carQueryRow = mysql_fetch_array ( $res ) ) {
		$carModel = new CarModel ();
		$carModel->setCarId ( $carQueryRow ['id'] );
		$carModel->setCompany ( $carQueryRow [CAR_COMPANY] );
		$carModel->setCarModel ( $carQueryRow [CAR_MODEL] );
		$carModel->setCarColor ( $carQueryRow [CAR_COLOR] );
		$carModel->setLicensePlateNumber ( $carQueryRow [CAR_PLATE_NUMBER] );
		$carModel->setCarImageURL ( $carQueryRow [CAR_IMAGE] );
		array_push ( $out, $carModel->toJSON () );
	}
	sendResponse($out,  $carModel->toJSON () );
	}else{
		sendNoDataFound();
	}
}
?>