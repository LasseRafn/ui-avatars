<?php

ob_start('ob_gzhandler');

define( '__ROOT__', __DIR__ . '/..' );

require_once __ROOT__ . '/vendor/autoload.php';

header( 'Content-type: image/png' );
header( 'Pragma: public' );
header( 'Cache-Control: max-age=86400' );
header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + 86400 ) );

$avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();
$input  = new \Utils\Input;

if ( ! isset( $_GET['no-cache'] ) && file_exists( __ROOT__ . "/cache/{$input->cacheKey}.png" ) ) {
	$file = fopen( __ROOT__ . "/cache/{$input->cacheKey}.png", 'rb' );
	fpassthru( $file );

	exit;
}

$image = $avatar->name( $input->name )
                ->length( $input->length )
                ->fontSize( $input->fontSize )
                ->size( $input->size )
                ->background( $input->background )
                ->color( $input->color )
                ->smooth()
                ->rounded( $input->rounded )
                ->generate();

$image->save( __ROOT__ . "/cache/{$input->cacheKey}.png" );

echo $image->stream( 'png', 100 );

exit;
