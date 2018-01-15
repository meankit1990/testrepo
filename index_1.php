<?php
include_once 'config/dbCon.php';
include_once 'models/searchRideModel.php';
phpinfo();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$res = mysql_query("SELECT * FROM ridesAvailable");
	$row = mysql_fetch_array($res);
	$myArray = array();
	for($i=0; $i<count($res); $i++){
		$myArray[$i] = new SearchRide();
		$myArray[$i] -> setrideId($row['id']);
		$myArray[$i] -> setUserId($row['userId']);
		$myArray[$i] -> setAddressFrom($row['addressFrom']);
		$myArray[$i] -> setAddressTo($row['addressTo']);
		$out = [$myArray[$i]->toJSON()];
	}
	echo json_encode($out);   
}

?>