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

	private $hasQueryParameters = false;

	protected static $indexes = [
		'name',
		'size',
		'background',
		'color',
		'length',
		'font-size',
		'rounded'
	];

	public function __construct()
	{
		$this->detectQueryParameters();
		$this->detectUrlBasedParameters();

		$this->name       = $_GET['name'] ?? 'John Doe';
		$this->size       = (int) ( $_GET['size'] ?? 64 );
		$this->background = $_GET['background'] ?? '#ddd';
		$this->color      = $_GET['color'] ?? '#222';
		$this->length     = (int) ( $_GET['length'] ?? 2 );
		$this->fontSize   = (double) ( $_GET['font-size'] ?? 0.5 );

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

	private function detectQueryParameters()
	{
		foreach ( $_GET as $item => $value ) {
			if ( in_array( $item, self::$indexes, true ) ) {
				$this->hasQueryParameters = true;

				return true;
			}
		}

		return false;
	}

	private function detectUrlBasedParameters()
	{
		if ( $this->hasQueryParameters ) {
			return false;
		}

		$requestUrl = ltrim( $_SERVER['REQUEST_URI'], '/' );
		$requestUrl = ltrim( $requestUrl, 'api' );
		$requestUrl = ltrim( $requestUrl, '/' );

		$parameters = explode( '/', $requestUrl );

		foreach ( $parameters as $index => $value ) {
			if ( ! isset( self::$indexes[ $index ] ) ) {
				continue;
			}

			$_GET[ self::$indexes[ $index ] ] = urldecode($value);
		}
	}
}