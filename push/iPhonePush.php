<?php
error_reporting ( 0 );
function sendIOSPush($deviceToken, $pushJson,$msg) {

// 	$deviceToken = '2F701DA01531B250C539588D2B448C565F8E5292FBEC4BD250DFE4DB78587200';
	// Passphrase for the private key (ck.pem file)
	// $pass = '';
	// Get the parameters from http get or from command line
	$data = json_decode($pushJson);
	$message = $_GET ['message'] or $message = $argv [1] or $message = $data->userName. " ".$msg;
	$badge = ( int ) $_GET ['badge'] or $badge = ( int ) $argv [2];
	$sound = $_GET ['sound'] or $sound = $argv [3];
	// Construct the notification payload
	$body = array ();
	$body ['aps'] = array (
			'alert' => $message,
			'data' =>$pushJson
	);
	if ($badge)
		$body ['aps'] ['badge'] = $badge;
	if ($sound)
		$body ['aps'] ['sound'] = $sound;
		/* End of Configurable Items */
	$ctx = stream_context_create ();
// 	 '/../models/baseApiResponse.php'
	stream_context_set_option ( $ctx, 'ssl', 'local_cert', dirname ( __FILE__ ) .'/pushcert.pem' );
	// assume the private key passphase was removed.
	// stream_context_set_option($ctx, 'ssl', 'passphrase', $pass);
	$fp = stream_socket_client ( 'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx );
	if (! $fp) {
		print "Failed to connect $err $errstrn";
		return;
	} else {
		print "Connection OK";
	}
	$payload = json_encode ( $body );
	$msg = chr ( 0 ) . pack ( "n", 32 ) . pack ( 'H*', str_replace ( ' ', '', $deviceToken ) ) . pack ( "n", strlen ( $payload ) ) . $payload;
	print "sending message :" . $payload . "***";
	fwrite ( $fp, $msg );
	fclose ( $fp );
}
?>
