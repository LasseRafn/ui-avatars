<?php

define( '__ROOT__', __DIR__ . '/..' );

require_once __ROOT__ . '/vendor/autoload.php';

header( 'Content-type: application/json' );

$ch = curl_init();
curl_setopt( $ch, CURLOPT_URL, 'https://dashboard.apricot.dk/api/ui-avatars/5LJwwA7TqnHduCBgi0VmD3U7Yq4oyDZK' );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
curl_setopt( $ch, CURLOPT_USERAGENT, 'UI Avatars' );
curl_setopt( $ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST'] );

$response = json_decode( curl_exec( $ch ) );
curl_close( $ch );

echo json_encode( [
	'response_time' => round( $response->response_time, 4 ),
	'requests'      => number_format( $response->requests, 0 )
] );

exit;