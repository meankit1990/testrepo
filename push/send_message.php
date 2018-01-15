<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function sendPushNotifictaion($regId,$pushJson,$msg){
	
		include_once 'GCM.php';
		$gcm = new GCM ();
		$data = json_decode($pushJson);
		$message = $data->userName. " ".$msg;
		$registatoin_ids = array (
				$regId 
		);
		$message = array (
				"message" => array('alert' => $message,'data' =>$data));
		$result = $gcm->send_notification ( $registatoin_ids, $message );
		return $result;
}
?>
