<?php
function sendOtp($sendTo,$otp){
	$request = ""; // initialise the request variable
	$param ['method'] = "sendMessage";
	$param ['send_to'] = $sendTo;
	$param ['msg'] = "Hello, Welcome to Carpool. Your OTP for the registration is ".$otp;
	$param ['userid'] = "2000156852";
	$param ['password'] = "mO2pHkSSf";
	$param ['v'] = "1.1";
	$param ['format'] = "JSON";
	$param ['msg_type'] = "TEXT"; // Can be "FLASH”/"UNICODE_TEXT"/”BINARY”
	$param ['auth_scheme'] = "PLAIN";
	// Have to URL encode the values
	foreach ( $param as $key => $val ) {
		$request .= $key . "=" . urlencode ( $val );
		// we have to urlencode the values
		$request .= "&";
		// append the ampersand (&) sign after each
		// parameter/value pair
	}
	$request = substr ( $request, 0, strlen ( $request ) - 1 );
	// remove final (&) sign from the request
	$url = "http://enterprise.smsgupshup.com/GatewayAPI/rest?" . $request;
	$ch = curl_init ( $url );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$curl_scraped_page = curl_exec ( $ch );
	curl_close ( $ch );
	return $curl_scraped_page;
}
?> 