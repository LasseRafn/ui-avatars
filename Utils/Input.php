<?php namespace Utils;

class Input
{
	public $name;
	public $length;
	public $size;
	public $fontSize;
	public $background;
	public $color;
	public $cacheKey;

	public function __construct()
	{
		$this->name       = $_GET['name'] ?? 'John Doe';
		$this->length     = (int) ( $_GET['length'] ?? 2 );
		$this->size       = (int) ( $_GET['size'] ?? 64 );
		$this->fontSize   = (double) ( $_GET['font-size'] ?? 0.5 );
		$this->background = $_GET['background'] ?? '#000';
		$this->color      = $_GET['color'] ?? '#fff';

		$this->fixInvalidInput();
		$this->generateCacheKey();
	}

	private function generateCacheKey()
	{
		$this->cacheKey = md5( "{$this->initials}-{$this->length}-{$this->size}-{$this->fontSize}-{$this->background}-{$this->color}" );
	}

	private function fixInvalidInput()
	{
		if ( $this->length <= 0 ) {
			$this->length = 1;
		}

		if ( $this->size <= 0 ) {
			$this->size = 0.5;
		}
	}
}