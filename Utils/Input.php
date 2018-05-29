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
	public $uppercase;

	private $hasQueryParameters = false;

	protected static $indexes = [
		'name',
		'size',
		'background',
		'color',
		'length',
		'font-size',
		'rounded',
		'uppercase',
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
		$this->getUppercase();
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

	private function getUppercase()
	{
		$uppercase = $_GET['uppercase'] ?? true;

		if ( is_bool( $uppercase ) ) {
			$this->uppercase = $uppercase;

			return;
		}

		switch ( $uppercase ) {
			case 'true':
			case 1:
			case '1':
			case 'yes':
			default:
				$this->uppercase = true;
				break;

			case 'false':
			case 0:
			case '0':
			case 'no':
				$this->uppercase = false;
				break;
		}
	}

	private function getInitials()
	{
		$this->initials = ( new Initials )->length( $this->length )->keepCase(!$this->uppercase)->generate( $this->name );
	}

	private function generateCacheKey()
	{
		$this->cacheKey = md5( "{$this->initials}-{$this->length}-{$this->size}-{$this->fontSize}-{$this->background}-{$this->color}-{$this->rounded}-{$this->uppercase}" );
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

		if ( $this->size > 512 ) {
			$this->size = 512;
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