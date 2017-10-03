<?php namespace Utils;

use LasseRafn\Initials\Initials;

class Input
{
	public $name;
	public $length;
	public $size;
	public $fontSize;
	public $background;
	public $color;
	public $cacheKey;
	public $rounded;

	public function __construct()
	{
		$this->detectBase64();

		$this->name       = $_GET['name'] ?? 'John Doe';
		$this->length     = (int) ( $_GET['length'] ?? 2 );
		$this->size       = (int) ( $_GET['size'] ?? 64 );
		$this->fontSize   = (double) ( $_GET['font-size'] ?? 0.5 );
		$this->background = $_GET['background'] ?? '#ddd';
		$this->color      = $_GET['color'] ?? '#222';

		$this->getRounded();
		$this->getInitials();
		$this->fixInvalidInput();
		$this->generateCacheKey();
	}

	private function getRounded()
	{
		$rounded = $_GET['rounded'] ?? false;

		if ( is_bool( $rounded ) ) {
			$this->rounded = $rounded;

			return;
		}

		switch ( $rounded ) {
			case 'true':
			case 1:
			case '1':
			case 'yes':
				$this->rounded = true;
				break;

			case 'false':
			case 0:
			case '0':
			case 'no':
			default:
				$this->rounded = false;
				break;
		}
	}

	private function getInitials()
	{
		$this->initials = ( new Initials )->length( $this->length )->generate( $this->name );
	}

	private function generateCacheKey()
	{
		$this->cacheKey = md5( "{$this->initials}-{$this->length}-{$this->size}-{$this->fontSize}-{$this->background}-{$this->color}-{$this->rounded}" );
	}

	private function fixInvalidInput()
	{
		if ( $this->length <= 0 ) {
			$this->length = 1;
		}

		if ( $this->fontSize <= 0 ) {
			$this->fontSize = 0.5;
		}

		if ( $this->fontSize > 1 ) {
			$this->fontSize = 1;
		}

		if ( $this->size <= 15 ) {
			$this->size = 16;
		}

		if ( $this->size > 256 ) {
			$this->size = 256;
		}
	}

	private function detectBase64() {
		$requestUrl = ltrim( $_SERVER['REQUEST_URI'], '/' );
		$requestUrl = ltrim( $requestUrl, 'api' );
		$requestUrl = ltrim( $requestUrl, '/' );
		$isBase64   = base64_encode( base64_decode( $requestUrl, true ) ) === $requestUrl;

		if ( !$isBase64 ) {
			$requestUrl = urldecode($requestUrl);
			$isBase64   = base64_encode( base64_decode( $requestUrl, true ) ) === $requestUrl;
		}

		if ( $isBase64 ) {
			parse_str( base64_decode( $requestUrl, true ), $_GET );
		}
	}
}