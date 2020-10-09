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
	public $initials;
	public $bold;
	public $format;

	private $hasQueryParameters = false;

	private $textColorRandom = '#222';

	private static $indexes = [
		'name',
		'size',
		'background',
		'color',
		'length',
		'font-size',
		'rounded',
		'uppercase',
		'bold',
		'format'
	];

	public function __construct()
	{
		$this->detectQueryParameters();
		$this->detectUrlBasedParameters();

		$this->name       = $_GET['name'] ?? 'John Doe';
		$this->size       = (int) ( $_GET['size'] ?? 64 );
		$this->color      = $this->getTextColor();
		$this->background = $this->getBackground();
		$this->length     = (int) ( $_GET['length'] ?? 2 );
		$this->fontSize   = (double) ( $_GET['font-size'] ?? 0.5 );
		$this->bold      = $this->getBold();
		$this->rounded   = $this->getRounded();
		$this->uppercase = $this->getUppercase();
		$this->initials  = $this->getInitials();
		$this->format    = $this->getFormat();
		$this->cacheKey  = $this->generateCacheKey();
		$this->fixInvalidInput();
	}

	private function getRounded()
	{
		return filter_var( $_GET['rounded'] ?? false, FILTER_VALIDATE_BOOLEAN );
	}

	private function getBold()
	{
		return filter_var( $_GET['bold'] ?? false, FILTER_VALIDATE_BOOLEAN );
	}

	private function getBackground()
	{
		if ( $_GET['background'] === 'random' ) {
			$colors = [
				[ 'b' => '5e35b1', 't' => 'FFFFFF', ],
				[ 'b' => '512da8', 't' => 'FFFFFF', ],
				[ 'b' => '4527a0', 't' => 'FFFFFF', ],
				[ 'b' => '311b92', 't' => 'FFFFFF', ],
				[ 'b' => '8e24aa', 't' => 'FFFFFF', ],
				[ 'b' => '7b1fa2', 't' => 'FFFFFF', ],
				[ 'b' => '6a1b9a', 't' => 'FFFFFF', ],
				[ 'b' => '4a148c', 't' => 'FFFFFF', ],
				[ 'b' => '3949ab', 't' => 'FFFFFF', ],
				[ 'b' => '303f9f', 't' => 'FFFFFF', ],
				[ 'b' => '283593', 't' => 'FFFFFF', ],
				[ 'b' => '1a237e', 't' => 'FFFFFF', ],
				[ 'b' => '1e88e5', 't' => 'FFFFFF', ],
				[ 'b' => '1976d2', 't' => 'FFFFFF', ],
				[ 'b' => '1565c0', 't' => 'FFFFFF', ],
				[ 'b' => '0d47a1', 't' => 'FFFFFF', ],
				[ 'b' => '039be5', 't' => 'FFFFFF', ],
				[ 'b' => '0288d1', 't' => 'FFFFFF', ],
				[ 'b' => '0277bd', 't' => 'FFFFFF', ],
				[ 'b' => '01579b', 't' => 'FFFFFF', ],
				[ 'b' => '00acc1', 't' => 'FFFFFF', ],
				[ 'b' => '0097a7', 't' => 'FFFFFF', ],
				[ 'b' => '00838f', 't' => 'FFFFFF', ],
				[ 'b' => '006064', 't' => 'FFFFFF', ],
				[ 'b' => '00897b', 't' => 'FFFFFF', ],
				[ 'b' => '00796b', 't' => 'FFFFFF', ],
				[ 'b' => '00695c', 't' => 'FFFFFF', ],
				[ 'b' => '004d40', 't' => 'FFFFFF', ],
				[ 'b' => '43a047', 't' => 'FFFFFF', ],
				[ 'b' => '388e3c', 't' => 'FFFFFF', ],
				[ 'b' => '2e7d32', 't' => 'FFFFFF', ],
				[ 'b' => '1b5e20', 't' => 'FFFFFF', ],
				[ 'b' => '7cb342', 't' => 'FFFFFF', ],
				[ 'b' => '689f38', 't' => 'FFFFFF', ],
				[ 'b' => '558b2f', 't' => 'FFFFFF', ],
				[ 'b' => '33691e', 't' => 'FFFFFF', ],
				[ 'b' => 'c0ca33', 't' => 'FFFFFF', ],
				[ 'b' => 'afb42b', 't' => 'FFFFFF', ],
				[ 'b' => '9e9d24', 't' => 'FFFFFF', ],
				[ 'b' => '827717', 't' => 'FFFFFF', ],
				[ 'b' => 'fdd835', 't' => 'FFFFFF', ],
				[ 'b' => 'fbc02d', 't' => 'FFFFFF', ],
				[ 'b' => 'f9a825', 't' => 'FFFFFF', ],
				[ 'b' => 'f57f17', 't' => 'FFFFFF', ],
				[ 'b' => 'ffb300', 't' => 'FFFFFF', ],
				[ 'b' => 'ffa000', 't' => 'FFFFFF', ],
				[ 'b' => 'ff8f00', 't' => 'FFFFFF', ],
				[ 'b' => 'ff6f00', 't' => 'FFFFFF', ],
				[ 'b' => 'fb8c00', 't' => 'FFFFFF', ],
				[ 'b' => 'f57c00', 't' => 'FFFFFF', ],
				[ 'b' => 'ef6c00', 't' => 'FFFFFF', ],
				[ 'b' => 'e65100', 't' => 'FFFFFF', ],
				[ 'b' => 'f4511e', 't' => 'FFFFFF', ],
				[ 'b' => 'e64a19', 't' => 'FFFFFF', ],
				[ 'b' => 'd84315', 't' => 'FFFFFF', ],
				[ 'b' => 'bf360c', 't' => 'FFFFFF', ],
				[ 'b' => '6d4c41', 't' => 'FFFFFF', ],
				[ 'b' => '5d4037', 't' => 'FFFFFF', ],
				[ 'b' => '4e342e', 't' => 'FFFFFF', ],
				[ 'b' => '3e2723', 't' => 'FFFFFF', ],
				[ 'b' => '546e7a', 't' => 'FFFFFF', ],
				[ 'b' => '455a64', 't' => 'FFFFFF', ],
				[ 'b' => '37474f', 't' => 'FFFFFF', ],
				[ 'b' => '263238', 't' => 'FFFFFF', ],
				[ 'b' => 'F44336', 't' => 'FFFFFF', ],
				[ 'b' => 'E53935', 't' => 'FFFFFF', ],
				[ 'b' => 'D32F2F', 't' => 'FFFFFF', ],
				[ 'b' => 'C62828', 't' => 'FFFFFF', ],
				[ 'b' => 'B71C1C', 't' => 'FFFFFF', ],
				[ 'b' => 'FFEBEE', 't' => '000000', ],
				[ 'b' => 'FFCDD2', 't' => '000000', ],
				[ 'b' => 'EF9A9A', 't' => '000000', ],
				[ 'b' => 'E57373', 't' => '000000', ],
				[ 'b' => 'EF5350', 't' => '000000', ],
				[ 'b' => 'FF8A80', 't' => '000000', ],
				[ 'b' => 'FF5252', 't' => '000000', ],
				[ 'b' => 'FF1744', 't' => '000000', ],
				[ 'b' => 'D50000', 't' => '000000', ],
				[ 'b' => 'FCE4EC', 't' => '000000', ],
				[ 'b' => 'F8BBD0', 't' => '000000', ],
				[ 'b' => 'F48FB1', 't' => '000000', ],
				[ 'b' => 'F06292', 't' => '000000', ],
				[ 'b' => 'EC407A', 't' => '000000', ],
				[ 'b' => 'FF80AB', 't' => '000000', ],
				[ 'b' => 'FF4081', 't' => '000000', ],
				[ 'b' => 'F50057', 't' => '000000', ],
				[ 'b' => 'C51162', 't' => '000000', ],
				[ 'b' => 'D81B60', 't' => 'FFFFFF', ],
				[ 'b' => 'C2185B', 't' => 'FFFFFF', ],
				[ 'b' => 'AD1457', 't' => 'FFFFFF', ],
				[ 'b' => '880E4F', 't' => 'FFFFFF', ],
				[ 'b' => '9c27b0', 't' => '000000', ],
				[ 'b' => 'f3e5f5', 't' => '000000', ],
				[ 'b' => 'e1bee7', 't' => '000000', ],
				[ 'b' => 'ce93d8', 't' => '000000', ],
				[ 'b' => 'ba68c8', 't' => '000000', ],
				[ 'b' => 'ab47bc', 't' => '000000', ],
				[ 'b' => 'ea80fc', 't' => '000000', ],
				[ 'b' => 'e040fb', 't' => '000000', ],
				[ 'b' => 'd500f9', 't' => '000000', ],
				[ 'b' => 'aa00ff', 't' => '000000', ],
				[ 'b' => '673ab7', 't' => '000000', ],
				[ 'b' => 'ede7f6', 't' => '000000', ],
				[ 'b' => 'd1c4e9', 't' => '000000', ],
				[ 'b' => 'b39ddb', 't' => '000000', ],
				[ 'b' => '9575cd', 't' => '000000', ],
				[ 'b' => '7e57c2', 't' => '000000', ],
				[ 'b' => 'b388ff', 't' => '000000', ],
				[ 'b' => '7c4dff', 't' => '000000', ],
				[ 'b' => '651fff', 't' => '000000', ],
				[ 'b' => '6200ea', 't' => '000000', ],
				[ 'b' => '3f51b5', 't' => '000000', ],
				[ 'b' => 'e8eaf6', 't' => '000000', ],
				[ 'b' => 'c5cae9', 't' => '000000', ],
				[ 'b' => '9fa8da', 't' => '000000', ],
				[ 'b' => '7986cb', 't' => '000000', ],
				[ 'b' => '5c6bc0', 't' => '000000', ],
				[ 'b' => '8c9eff', 't' => '000000', ],
				[ 'b' => '536dfe', 't' => '000000', ],
				[ 'b' => '3d5afe', 't' => '000000', ],
				[ 'b' => '304ffe', 't' => '000000', ],
				[ 'b' => '2196f3', 't' => '000000', ],
				[ 'b' => 'e3f2fd', 't' => '000000', ],
				[ 'b' => 'bbdefb', 't' => '000000', ],
				[ 'b' => '90caf9', 't' => '000000', ],
				[ 'b' => '64b5f6', 't' => '000000', ],
				[ 'b' => '42a5f5', 't' => '000000', ],
				[ 'b' => '82b1ff', 't' => '000000', ],
				[ 'b' => '448aff', 't' => '000000', ],
				[ 'b' => '2979ff', 't' => '000000', ],
				[ 'b' => '2962ff', 't' => '000000', ],
				[ 'b' => '03a9f4', 't' => '000000', ],
				[ 'b' => 'e1f5fe', 't' => '000000', ],
				[ 'b' => 'b3e5fc', 't' => '000000', ],
				[ 'b' => '81d4fa', 't' => '000000', ],
				[ 'b' => '4fc3f7', 't' => '000000', ],
				[ 'b' => '29b6f6', 't' => '000000', ],
				[ 'b' => '80d8ff', 't' => '000000', ],
				[ 'b' => '40c4ff', 't' => '000000', ],
				[ 'b' => '00b0ff', 't' => '000000', ],
				[ 'b' => '0091ea', 't' => '000000', ],
				[ 'b' => '00bcd4', 't' => '000000', ],
				[ 'b' => 'e0f7fa', 't' => '000000', ],
				[ 'b' => 'b2ebf2', 't' => '000000', ],
				[ 'b' => '80deea', 't' => '000000', ],
				[ 'b' => '4dd0e1', 't' => '000000', ],
				[ 'b' => '26c6da', 't' => '000000', ],
				[ 'b' => '84ffff', 't' => '000000', ],
				[ 'b' => '18ffff', 't' => '000000', ],
				[ 'b' => '00e5ff', 't' => '000000', ],
				[ 'b' => '00b8d4', 't' => '000000', ],
				[ 'b' => '009688', 't' => '000000', ],
				[ 'b' => 'e0f2f1', 't' => '000000', ],
				[ 'b' => 'b2dfdb', 't' => '000000', ],
				[ 'b' => '80cbc4', 't' => '000000', ],
				[ 'b' => '4db6ac', 't' => '000000', ],
				[ 'b' => '26a69a', 't' => '000000', ],
				[ 'b' => 'a7ffeb', 't' => '000000', ],
				[ 'b' => '64ffda', 't' => '000000', ],
				[ 'b' => '1de9b6', 't' => '000000', ],
				[ 'b' => '00bfa5', 't' => '000000', ],
				[ 'b' => '4caf50', 't' => '000000', ],
				[ 'b' => 'e8f5e9', 't' => '000000', ],
				[ 'b' => 'c8e6c9', 't' => '000000', ],
				[ 'b' => 'a5d6a7', 't' => '000000', ],
				[ 'b' => '81c784', 't' => '000000', ],
				[ 'b' => '66bb6a', 't' => '000000', ],
				[ 'b' => 'b9f6ca', 't' => '000000', ],
				[ 'b' => '69f0ae', 't' => '000000', ],
				[ 'b' => '00e676', 't' => '000000', ],
				[ 'b' => '00c853', 't' => '000000', ],
				[ 'b' => '8bc34a', 't' => '000000', ],
				[ 'b' => 'f1f8e9', 't' => '000000', ],
				[ 'b' => 'dcedc8', 't' => '000000', ],
				[ 'b' => 'c5e1a5', 't' => '000000', ],
				[ 'b' => 'aed581', 't' => '000000', ],
				[ 'b' => '9ccc65', 't' => '000000', ],
				[ 'b' => 'ccff90', 't' => '000000', ],
				[ 'b' => 'b2ff59', 't' => '000000', ],
				[ 'b' => '76ff03', 't' => '000000', ],
				[ 'b' => '64dd17', 't' => '000000', ],
				[ 'b' => 'cddc39', 't' => '000000', ],
				[ 'b' => 'f9fbe7', 't' => '000000', ],
				[ 'b' => 'f0f4c3', 't' => '000000', ],
				[ 'b' => 'e6ee9c', 't' => '000000', ],
				[ 'b' => 'dce775', 't' => '000000', ],
				[ 'b' => 'd4e157', 't' => '000000', ],
				[ 'b' => 'f4ff81', 't' => '000000', ],
				[ 'b' => 'eeff41', 't' => '000000', ],
				[ 'b' => 'c6ff00', 't' => '000000', ],
				[ 'b' => 'aeea00', 't' => '000000', ],
				[ 'b' => 'ffeb3b', 't' => '000000', ],
				[ 'b' => 'fffde7', 't' => '000000', ],
				[ 'b' => 'fff9c4', 't' => '000000', ],
				[ 'b' => 'fff59d', 't' => '000000', ],
				[ 'b' => 'fff176', 't' => '000000', ],
				[ 'b' => 'ffee58', 't' => '000000', ],
				[ 'b' => 'ffff8d', 't' => '000000', ],
				[ 'b' => 'ffff00', 't' => '000000', ],
				[ 'b' => 'ffea00', 't' => '000000', ],
				[ 'b' => 'ffd600', 't' => '000000', ],
				[ 'b' => 'ffc107', 't' => '000000', ],
				[ 'b' => 'fff8e1', 't' => '000000', ],
				[ 'b' => 'ffecb3', 't' => '000000', ],
				[ 'b' => 'ffe082', 't' => '000000', ],
				[ 'b' => 'ffd54f', 't' => '000000', ],
				[ 'b' => 'ffca28', 't' => '000000', ],
				[ 'b' => 'ffe57f', 't' => '000000', ],
				[ 'b' => 'ffd740', 't' => '000000', ],
				[ 'b' => 'ffc400', 't' => '000000', ],
				[ 'b' => 'ffab00', 't' => '000000', ],
				[ 'b' => 'ff9800', 't' => '000000', ],
				[ 'b' => 'fff3e0', 't' => '000000', ],
				[ 'b' => 'ffe0b2', 't' => '000000', ],
				[ 'b' => 'ffcc80', 't' => '000000', ],
				[ 'b' => 'ffb74d', 't' => '000000', ],
				[ 'b' => 'ffa726', 't' => '000000', ],
				[ 'b' => 'ffd180', 't' => '000000', ],
				[ 'b' => 'ffab40', 't' => '000000', ],
				[ 'b' => 'ff9100', 't' => '000000', ],
				[ 'b' => 'ff6d00', 't' => '000000', ],
				[ 'b' => 'ff5722', 't' => '000000', ],
				[ 'b' => 'fbe9e7', 't' => '000000', ],
				[ 'b' => 'ffccbc', 't' => '000000', ],
				[ 'b' => 'ffab91', 't' => '000000', ],
				[ 'b' => 'ff8a65', 't' => '000000', ],
				[ 'b' => 'ff7043', 't' => '000000', ],
				[ 'b' => 'ff9e80', 't' => '000000', ],
				[ 'b' => 'ff6e40', 't' => '000000', ],
				[ 'b' => 'ff3d00', 't' => '000000', ],
				[ 'b' => 'dd2c00', 't' => '000000', ],
				[ 'b' => '795548', 't' => '000000', ],
				[ 'b' => 'efebe9', 't' => '000000', ],
				[ 'b' => 'd7ccc8', 't' => '000000', ],
				[ 'b' => 'bcaaa4', 't' => '000000', ],
				[ 'b' => 'a1887f', 't' => '000000', ],
				[ 'b' => '8d6e63', 't' => '000000', ],
				[ 'b' => '607d8b', 't' => '000000', ],
				[ 'b' => 'eceff1', 't' => '000000', ],
				[ 'b' => 'cfd8dc', 't' => '000000', ],
				[ 'b' => 'b0bec5', 't' => '000000', ],
				[ 'b' => '90a4ae', 't' => '000000', ],
				[ 'b' => '78909c', 't' => '000000', ],
				[ 'b' => '9e9e9e', 't' => '000000', ],
				[ 'b' => 'fafafa', 't' => '000000', ],
				[ 'b' => 'f5f5f5', 't' => '000000', ],
				[ 'b' => 'eeeeee', 't' => '000000', ],
				[ 'b' => 'e0e0e0', 't' => '000000', ],
				[ 'b' => 'bdbdbd', 't' => '000000', ],
			];

			$randomColor = $colors[ random_int( 0, \count( $colors ) - 1 ) ];
			$this->setTextColor( $randomColor["t"] );

			return $randomColor["b"];
		}

		return $_GET['background'] ?? '#ddd';
	}

	private function setTextColor( $color )
	{
		$this->color = $color ?? $this->getTextColor();
	}

	private function getTextColor()
	{
		return $_GET['color'] ?? '#222';
	}

	private function getUppercase()
	{
		return filter_var( $_GET['uppercase'] ?? true, FILTER_VALIDATE_BOOLEAN );
	}

	private function getInitials()
	{
		return ( new Initials )->length( $this->length )->keepCase( ! $this->uppercase )->generate( $this->name );
	}

	private function getFormat()
	{
		if ( in_array( $_GET['format'] ?? '', [ 'png', 'svg' ], true ) ) {
			return $_GET['format'];
		}

		return strpos( $_SERVER['HTTP_ACCEPT'] ?? $_REQUEST['Accept'] ?? '', 'image/svg+xml' ) !== false ? 'svg' : 'png';
	}

	private function generateCacheKey()
	{
		return md5( "{$this->initials}-{$this->length}-{$this->size}-{$this->fontSize}-{$this->background}-{$this->color}-{$this->rounded}-{$this->uppercase}-{$this->bold}" );
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
			if ( \in_array( $item, self::$indexes, true ) ) {
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

		foreach ( explode( '/', $requestUrl ) as $index => $value ) {
			if ( ! isset( self::$indexes[ $index ] ) ) {
				continue;
			}

			$_GET[ self::$indexes[ $index ] ] = urldecode( $value );
		}

		return true;
	}
}
