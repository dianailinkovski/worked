<?php
/**
 * A class for handling and generating colors
 */
class Color_handler {

	private static $_position = -1;

	protected static $_colors;
	protected static $_order;

	/**
	 * Initialize the color handler with an array of arrays
	 * in the format array('hex' => hex, 'string' => string)
	 *
	 * @param array $colors
	 */
	public static function init(array $colors) {
		self::$_order = array();
		foreach ($colors as $color_info) {
			$hex = NULL;
			$string = NULL;
			if (isset($color_info['hex']))
				$hex = $color_info['hex'];
			if (isset($color_info['string']))
				$string = $color_info['string'];

			self::add_color(new Color($hex, $string));
		}
	}

	/**
	 * Add a color to the colors array
	 *
	 * @param Color $color
	 */
	public static function add_color(Color $color) {
		if ( ! is_array(self::$_colors))
			self::$_colors = array();

		self::$_colors[$color->get_hex()] = $color;
		self::$_order[] = $color->get_hex();
	}

	/**
	 * Add a collection of colors to the colors array
	 *
	 * @param array $colors
	 */
	public static function add_colors(array $colors) {
		foreach ($colors as $color) {
			self::add_color($color);
		}
	}

	/**
	 * Get the colors array
	 *
	 * @return array
	 */
	public static function get_colors() {
		return self::$_colors;
	}

	/**
	 * Get the next color in the array
	 * (Uses the array internal pointer)
	 *
	 * @return Color
	 */
	public static function get_next($position = NULL) {
		if (isset($position)) {
			$tmp_position = self::$_position;
			self::$_position = $position - 1;
		}

		$next = self::next();

		if (isset($position))
			self::$_position = $tmp_position;

		if ( ! $next) {
			$new_color = self::random_color();
			while(isset(self::$_colors[$new_color->get_hex()]))
				$new_color = self::random_color();

			self::add_color($new_color);
			$next = self::get_next();
		}

		return $next;
	}

	/**
	 * Create a random colored Color object
	 *
	 * @return Color
	 */
	public static function random_color() {
		return new Color(Color::random_color());
	}

	/*
	 * The Iterator
	 */
	public static function rewind() {
		self::$_position = 0;
	}

	public static function current() {
		return self::$_colors[self::key()];
	}

	public static function key() {
		return self::$_order[self::$_position];
	}

	public static function next() {
		self::$_position++;
		if ( ! self::valid()) {
			self::$_position--;

			return FALSE;
		}

		return self::current();
	}

	public function valid() {
		return isset(self::$_order[self::$_position]);
	}
}

/**
 * A simple color representation
 *
 * @uses Color_handler
 */
class Color {

	protected $hex;
	protected $string;
	protected $r;
	protected $g;
	protected $b;

	public function __construct($hex = NULL, $string = NULL) {
		if ( ! isset($hex)) {
			$hex = '000000';
			$string = 'black';
		}

		$this->set_hex($hex, $string);
	}

	/**
	 * Set the color using the hexadecimal format
	 *
	 * @param String $hex
	 * @param String $string { default : NULL }
	 */
	public function set_hex($hex, $string = NULL) {
		$this->hex = strtoupper(str_replace('#', '', $hex));
		$this->string = $string;

		$rgb = self::hex_to_rgb($hex);
		$this->r = isset($rgb['r']) ? $rgb['r'] : 0;
		$this->g = isset($rgb['g']) ? $rgb['g'] : 0;
		$this->b = isset($rgb['b']) ? $rgb['b'] : 0;
	}

	/**
	 * Set the color using the RGB format
	 *
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @param String $string { default : NULL }
	 */
	public function set_rgb($r, $g, $b, $string = NULL) {
		$this->r = (int)$r;
		$this->g = (int)$g;
		$this->b = (int)$b;
		$this->string = $string;

		$this->hex = self::rgb_to_hex($r, $g, $b);
	}

	/**
	 * Get the color in hexadecimal format
	 *
	 * @return String
	 */
	public function get_hex($include_pound = TRUE) {
		return self::prep_hex_color($this->hex, $include_pound);
	}

	/**
	 * Get the color in RGB format
	 *
	 * @return array
	 */
	public function get_rgb() {
		return array(
			'r' => $this->r,
			'g' => $this->g,
			'b' => $this->b
		);
	}

	/**
	 * Set the human name of the color
	 *
	 * @param String $string
	 */
	public function set_string($string) {
		$this->string = $string;
	}

	/**
	 * Get the human name of the color
	 *
	 * @return String
	 */
	public function get_string() {
		return $this->string;
	}

	/**
	 * Check whether a string is in a hexadecimal color format
	 *
	 * @param String $hex
	 * @return boolean
	 */
	public static function is_valid_hex($hex) {
		return (boolean)preg_match('/^#?[A-F0-9]{6}$|^#?[A-F0-9]{3}$/i', $hex);
	}

	/**
	 * Get the hexadecimal string in all caps with the '#'
	 *
	 * @param String $hex
	 * @return String
	 */
	protected static function prep_hex_color($hex, $include_pound = TRUE) {
		if ($hex{0} !== '#' AND $include_pound)
			$hex = '#' . $hex;
		elseif ($hex{0} === '#' AND ! $include_pound)
			$hex = substr ($hex, 1);

		return strtoupper($hex);
	}

	/**
	 * Get the hexadecimal string in all caps without the '#'
	 *
	 * @param String $hex
	 * @return String
	 */
	protected static function prep_hex($hex) {
		return strtoupper(str_replace('#', '', $hex));
	}

	/**
	 * Convert a Hexadecimal color to its RGB equivalent
	 *
	 * @param String $hex
	 * @return array
	 */
	public static function hex_to_rgb($hex) {
		if ( ! Color::is_valid_hex($hex))
			throw UnexpectedValueException($hex . ' is not a valid hexadecimal color. It must be a 3 or 6 character hexadecimal number');

		$hex = str_replace('#', '', $hex);
		$color = array();

		if(strlen($hex) === 3) {
			$color['r'] = (int)hexdec(substr($hex, 0, 1));
			$color['g'] = (int)hexdec(substr($hex, 1, 1));
			$color['b'] = (int)hexdec(substr($hex, 2, 1));
		}
		elseif(strlen($hex) === 6) {
			$color['r'] = (int)hexdec(substr($hex, 0, 2));
			$color['g'] = (int)hexdec(substr($hex, 2, 2));
			$color['b'] = (int)hexdec(substr($hex, 4, 2));
		}
		else {
			$color = self::hex_to_rgb('000');
		}

		return $color;
	}

	/**
	 * Convert RGB color to its Hexadecimal equivalent
	 *
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @return String
	 */
	public static function rgb_to_hex($r, $g, $b) {
		$hex = '#';
		$hex.= str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		$hex.= str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		$hex.= str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

		return $hex;
	}

	/**
	 * Generate a random color in hexadecimal format
	 *
	 * @return String
	 */
	public static function random_color() {
		return self::prep_hex_color(substr(md5(rand()), 0, 6));
	}
}
