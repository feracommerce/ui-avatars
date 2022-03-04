<?php

ob_start( 'ob_gzhandler' );

define( '__ROOT__', __DIR__ . '/..' );

require_once __ROOT__ . '/vendor/autoload.php';

error_reporting(E_ALL);
ini_set("display_errors", 1);

header( 'Pragma: public' );
header( 'Access-Control-Allow-Origin: *' );
header( 'Access-Control-Allow-Credentials: true' );
header( 'Access-Control-Allow-Methods: GET, OPTIONS' );
header( 'Access-Control-Max-Age: 1814400' );
header( 'Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, remember-me' );
header( 'Cache-Control: max-age=1814400' );

$input  = new \Utils\Input;
$avatar = new LasseRafn\InitialAvatarGenerator\InitialAvatar();

if ( $input->format === 'svg' ) {
	header( 'Content-type: image/svg+xml' );

	echo '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="' . $input->size . 'px" height="' . $input->size . 'px" viewBox="0 0 ' . $input->size . ' ' . $input->size . '" version="1.1"><' . ( $input->rounded ? 'circle' : 'rect' ) . ' fill="#' . trim( $input->background, '#' ) . '" cx="' . ( $input->size / 2 ) . '" width="' . $input->size . '" height="' . $input->size . '" cy="' . ( $input->size / 2 ) . '" r="' . ( $input->size / 2 ) . '"/><text x="50%" y="50%" style="color: #' . trim( $input->color, '#' ) . '; line-height: 1;font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', \'Roboto\', \'Oxygen\', \'Ubuntu\', \'Fira Sans\', \'Droid Sans\', \'Helvetica Neue\', sans-serif;" alignment-baseline="middle" text-anchor="middle" font-size="' . round( $input->size * $input->fontSize ) . '" font-weight="' . ( $input->bold ? 600 : 400 ) . '" dy=".1em" dominant-baseline="middle" fill="#' . trim( $input->color, '#' ) . '">' . $avatar->name( $input->name );
	return;
} else {
	header( 'Content-type: image/png' );
}

if ( ! isset( $_GET['no-cache'] ) && file_exists( __ROOT__ . "/cache/{$input->cacheKey}.png" ) ) {

	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', filemtime( __ROOT__ . "/cache/{$input->cacheKey}.png" ) + 1814400 ) );

	$file = fopen( __ROOT__ . "/cache/{$input->cacheKey}.png", 'rb' );
	fpassthru( $file );

	exit;
}

header( 'Expires: ' . gmdate( 'D, d M Y H:i:s \G\M\T', time() + 1814400 ) );

$image = $avatar->name( $input->name )
                ->length( $input->length )
                ->fontSize( $input->fontSize )
                ->size( $input->size )
                ->background( $input->background )
                ->color( $input->color )
                ->smooth()
                ->allowSpecialCharacters( false )
                ->autoFont()
                ->keepCase( ! $input->uppercase )
                ->rounded( $input->rounded );

if ( $input->bold ) {
	$image = $image->preferBold();
}

$image = $image->generate();

$image->save( __ROOT__ . "/cache/{$input->cacheKey}.png", 100 );

echo $image->stream( 'png', 100 );

// if ( isset( $_GET['debug'] ) ) {
// 	echo $image->stream( 'png', 100 );
// } else {
// 	header( 'X-Accel-Redirect: ' . "/cache/{$input->cacheKey}.png" );
// }

exit;
