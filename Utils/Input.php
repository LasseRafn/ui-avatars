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
		$this->initials = ( new Initials )->generate( $this->name );
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

		if ( $this->size <= 0 ) {
			$this->size = 16;
		}

		if ( $this->size > 256 ) {
			$this->size = 256;
		}
	}
}