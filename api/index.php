<?php

ob_start( 'ob_gzhandler' );

define( '__ROOT__', __DIR__ . '/..' );

require_once __ROOT__ . '/vendor/autoload.php';

header( 'Content-type: image/png' );
header( 'Pragma: public' );
header( 'Cache-Control: max-age=172800' );

$avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();
$input  = new \Utils\Input;

if ( ! isset( $_GET['no-cache'] ) && file_exists( __ROOT__ . "/cache/{$input->cacheKey}.png" ) ) {
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', filemtime( __ROOT__ . "/cache/{$input->cacheKey}.png" ) + 172800 ) );
	header('X-Accel-Redirect: ' .  "/cache/{$input->cacheKey}.png"); // If this part is causing you trouble, remove it and uncomment the two following lines:
//	$file = fopen( __ROOT__ . "/cache/{$input->cacheKey}.png", 'rb' );
//	fpassthru( $file );

	exit;
}
else {
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + 172800 ) );
}

$image = $avatar->name( $input->name )
                ->length( $input->length )
                ->fontSize( $input->fontSize )
                ->size( $input->size )
                ->background( $input->background )
                ->color( $input->color )
                ->smooth()
                ->autoFont()
                ->rounded( $input->rounded )
                ->generate();

$image->save( __ROOT__ . "/cache/{$input->cacheKey}.png", 100 );

header('X-Accel-Redirect: ' .  "/cache/{$input->cacheKey}.png"); // If this part is causing you trouble, remove it and uncomment the following line:
//echo $image->stream( 'png', 100 );

exit;
