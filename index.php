<?php

// Report ALL errors
error_reporting( E_ALL );
ini_set( 'error_reporting', E_ALL );
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( - 1 );
require_once __DIR__ . '/vendor/autoload.php';

header( 'Content-type: image/png' );

$avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();
$input  = new \Utils\Input;

if ( file_exists( __DIR__ . "/cache/{$input->cacheKey}.png" ) ) {
	echo readfile( __DIR__ . "/cache/{$input->cacheKey}.png" );

	return;
}

$image = $avatar->name( $input->name )
                ->length( $input->length )
                ->fontSize( $input->fontSize )
                ->size( $input->size )
                ->background( $input->background )
                ->color( $input->color )
                ->generate();

$image->save( __DIR__ . "/cache/{$input->cacheKey}.png" );

echo $image->stream( 'png', 100 );
return;
