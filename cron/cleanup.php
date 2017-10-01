<?php

$files            = glob( __DIR__ . '/../cache/*.png' );
$now              = time();
$twoDaysInSeconds = 60 * 60 * 24 * 2;

foreach ( $files as $file ) {
	if ( is_file( $file ) ) {
		if ( $now - filemtime( $file ) >= $twoDaysInSeconds ) {
			unlink( $file );
		}
	}
}