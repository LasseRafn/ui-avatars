<?php

define( '__ROOT__', __DIR__ . '/..' );

require_once __ROOT__ . '/vendor/autoload.php';

header( 'Content-type: application/json' );

echo json_encode( [
	'response_time' => round( 2.8671792705, 4 ),
	'requests'      => number_format( 65000, 0 )
] );

exit;