<?php

/**
 *	Colorbar Coder
 *
 *	ColorbarCoder is PHP class for encoding the original text string to color codes
 *	and decoding the color codes from color bar image into original text.
 *
 *	The color codes can be constructed into color bar and the color bar can be downloaded
 *	as an image and saved it. ColorbarCoder loads the color bar image and detects the
 *	color codes from it. The color codes are then decoded into original text.
 *
 *	@author    Kelvin Yin <contact@kelvinyin.com>
 *	@version   v1.0.0
 *	@since     v1.0.0
 *	@copyright 2018 ColorbarCoder
 *	@license   https://github.com/Susros/ColorbarCoder/blob/master/LICENSE
 *
 *	Author website: https://www.kelvinyin.com
 */

class ColorbarCoder {

	/**
	 *	@var int Size of each hex code
	 */
	const HEX_SIZE = 6;

	/**
	 *	@var int ASCII code for space
	 */
	const ASCII_SPACE = 20;

	/**
	 *	@var int Bit to be shifted
	 */
	const SBIT = 1;

	/**
	 *	@var string[] Colorbar formatter
	 */
	const COLORBAR_FORMATTER = array(
		"107f7f",
		"107f1f",
		"7f1f7f",
		"7f1f11",
		"7f7f1d",
		"7f7f7f"
	);

	/**
	 *	@var string $text Original text to be encoded into color codes
	 */
	private $text;

	/**
	 *	@var string $hexText Hexidicimal encoded text of original text
	 */
	private $hexText;

	/**
	 *	@var string[] $hexCode Encoded hexidecimal for color code
	 */
	private $hexCode;

	/**
	 *	Constructor
	 *
	 *	Initiate all member variables
	 */
	public function __construct() {
		$this->text    = "";
		$this->hexText = "";
		$this->hexCode = array();
	}

	/**
	 *	Encode original text into color code
	 *
	 *	@param string $_text non-empty text input
	 *
	 *	@return ColorbarCoder Return this object to keep the chain
	 */
	public function encode($_text) {

		// Original text input
		$this->text = $_text;

		// Convert text into hexidecimal value
		$this->hexText = bin2hex($this->text);

		// Add space at the end of the text if the size of original text is not
		// divisible by size of hex code in order to form the format of hex code: XXYYZZ
		if (strlen($this->hexText) % ColorbarCoder::HEX_SIZE != 0) {
			$this->hexText .= str_repeat(ColorbarCoder::ASCII_SPACE, (ColorbarCoder::HEX_SIZE - (strlen($this->hexText) % ColorbarCoder::HEX_SIZE)) / 2);
		}

		// Chunk the hex text to get hex code and color code
		for ($i = 0; $i < strlen($this->hexText); $i += ColorbarCoder::HEX_SIZE) {
			$hex = substr($this->hexText, $i, ColorbarCoder::HEX_SIZE);

			// Add formatter
			$this->hexCode[] = $this->hexLeftBitShift(ColorbarCoder::COLORBAR_FORMATTER[rand(0, count(ColorbarCoder::COLORBAR_FORMATTER) - 1)]);
			$this->hexCode[] = $this->hexLeftBitShift($hex);
		}

		// This object to keep the chain
		return $this;
	}

	/**
	 *	Decode color code from color bar image
	 *
	 *	@param string $colorbar IMage location of color bar
	 *
	 *	@return ColorbarCoder Return this object to keep the chain
	 */
	public function decode($colorbar) {

		// Get the width and height of image
		$imageWidth  = getimagesize($colorbar)[0];
		$imageHeight = getimagesize($colorbar)[1];

		// Get image type
		$chunkImageFile = explode(".", $colorbar);
		$imageExtension = end($chunkImageFile);
		$imageExtension = strtolower($imageExtension);

		// Get image
		if ($imageExtension == "png") {
			$image = imagecreatefrompng($colorbar);
		} else {
			$this->EBreak("Error", "Only PNG is allowed for image");
		}

		// Extract image color
		$colorData = array();

		// Color data index
		$colorDataIndex = 0;

		for($i = 0; $i < $imageWidth; $i++) {
			
			// Index of image color of pixel
			$imageColorIndex = imagecolorat($image, $i, $imageHeight / 2);

			// Get color at the index
			$imageColor = imagecolorsforindex($image, $imageColorIndex);

			// Make sure no transparent
			if ($imageColor['alpha'] == 0) {

				// Construct hex color
				$hex = dechex($imageColor['red']) . dechex($imageColor['green']) . dechex($imageColor['blue']);

				$trueColor = $this->hexRightBitShift($hex);

				// Construct Color data
				if (in_array($trueColor, ColorbarCoder::COLORBAR_FORMATTER)) {
					if (isset($colorData[$colorDataIndex]) === true) {
						if (count($colorData[$colorDataIndex]) > 0) {
							$colorDataIndex++;
							$colorData[$colorDataIndex] = array();
						}
					} else {
						$colorData[$colorDataIndex] = array();
					}
				} else {
					if (isset($colorData[$colorDataIndex][$trueColor]) === true) {
						$colorData[$colorDataIndex][$trueColor]++;
					} else {
						$colorData[$colorDataIndex][$trueColor] = 1;
					}
				}

			}
		}

		// Decode the color
		foreach($colorData as $color) {
			
			// Get maximum weight of color code
			arsort($color);
			$colorCode = key($color);

			$this->hexText .= $colorCode;
		}

		// Decode hex string
		$this->text = trim(hex2bin($this->hexText));

		return $this;
	}

	//==========================================================
	//	Member Query Methods
	//==========================================================

	/**
	 *	Get the original text
	 *
	 *	@return string The original text input
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 *	Get the encoded hexidecimal text
	 *
	 *	@return string Encoded hexidecimal text
	 */
	public function getHexText() {
		return $this->hexText;
	}

	/**
	 *	Get the hexidecimal code
	 *
	 *	The format of the hex code is in XXYYZZ
	 *
	 *	@return string[] Encoded hexidecimal codes
	 */
	public function getHexCode() {
		return $this->hexCode;
	}

	/**
	 *	Get the color codes in hexidecimal format
	 *
	 *	@return string[] Hexidecimal color code
	 */
	public function getHexColorCode() {

		$colorCode = array();

		foreach($this->hexCode as $hex) {
			$colorCode[] = "#" . $hex;
		}

		return $colorCode;

	}

	/**
	 *	Get the color codes in RGB format
	 *
	 *	@return mix RGB format of color code
	 */
	public function getRGBColorCode() {

		$colorCode = array();

		foreach($this->hexCode as $hex) {
			$rbg = array(
				"r" => hexdec(substr($hex, 0, 2)),
				"g" => hexdec(substr($hex, 2, 2)),
				"b" => hexdec(substr($hex, 4, 2))
			);

			$colorCode[] = $rbg;
		}

		return $colorCode;

	}

	/**
	 *	Get color bar in HTML format
	 *
	 *	@param string $width  Width of container
	 *	@param string $height Height of container
	 *	@param string $id 	  ID of container
	 *
	 *	@return string Color bar constructed in HTML table
	 */
	public function getColorbarHTML($width = "500px", $height = "500px", $id = "") {

		// Default ID
		if ($id == "") {
			$id = uniqid();
		}

		// Color bar table wrapper
		$html = "<div class='colorbar-table-wrapper' id='colorbar-table-wrapper-". $id ."' style='width:". $width ."; height:" . $height ."'>";

		// Color bar table
		$html .= "<table id='colorbar-table-". $id ."' class='colorbar-table' style='width: 100%; height: 100%; border-collapse: collapse;'><tr>";

		foreach ($this->hexCode as $hex) {
            $html .= "<td style='padding: 0;'>";
            $html .= "<div class='colorbar' style='height: 100%; background: #". $hex .";'></div>";
            $html .= "</td>";
		}

		$html .="</tr></table>"; //End of color bar table

		$html .= "</div>"; // End of color bar table wrapper

		return $html;
	}

	/**
	 *	Print color bar in HTML format
	 *
	 *	@param string $width  Width of container
	 *	@param string $height Height of container
	 *	@param string $id 	  ID of container
	 */
	public function printColorbarHTML($width = "500px", $height = "500px", $id = "") {
		echo $this->getColorbarHTML($width, $height, $id);
	}

	/**
	 *	Print image out.
	 *
	 *	This method should be call on the top of the page before the content is printed.
	 *	
	 *	The type of image extension allowed are: JPEG, JPG, PNG
	 *
	 *	@param float  $width 	 Width of canvas
	 *	@param float  $height 	 Height of canvas
	 */
	public function printColorbarImage($width = 500, $height = 500) {

		$width     = floatval($width);
		$height    = floatval($height);
		$imageType = "png";

		// Image type header
		if ($imageType == 'png') {
			header('Content-Type: image/png');
		} else {
			$this->EBreak("Error", "Only PNG is allowed for image");
		}

		// Get colorbar image canvas
		$canvas = $this->drawColorbarImage($width, $height);

		// Print out image
		if ($imageType == 'png') {
			imagepng($canvas);
		}

		imagedestroy($canvas);
	}

	/**
	 *	Save image
	 *
	 *	@param float  $width 	 Width of canvas
	 *	@param float  $height 	 Height of canvas
	 *	@param string $dir 		 Directory where image to be saved
	 *
	 *	@return string Image file name
	 */
	public function saveColorbarImage($width = 500, $height = 500, $dir = "./") {

		$width     = floatval($width);
		$height    = floatval($height);
		$imageType = "png";
		$dir  	   = rtrim($dir, "/");

		// Get colorbar image canvas
		$canvas = $this->drawColorbarImage($width, $height);

		// Image name
		$imageName = md5(uniqid() . time());

		// Save image
		if ($imageType == 'png') {

			$imageName .= "." . $imageType;

			imagepng($canvas, $dir . "/" . $imageName);

		} else {
			$this->EBreak("Error", "Only PNG is allowed for image");
		}

		return $imageName;
	}

	//==========================================================
	//	Private Member Query Methods
	//==========================================================

	/**
	 *	Draw color bar image
	 *
	 *  @return resource|boolean Image resource identifier on success, false on fail
	 */
	private function drawColorbarImage($width, $height) {

		// Width and height of canvas
		$width  = floatval($width);
		$height = floatval($height);

		// Get image canvas
		$canvas = imagecreatetruecolor($width, $height);

		// Get track of current x coordinate
		$currentX = 0;

		// Get width of each bar
		$barWidth = $width / count($this->hexCode);

		// Draw image
		foreach($this->hexCode as $hex) {
			
			// x1 and x2 coordinate point
			$x1 = $currentX;
			$x2 = $currentX + $barWidth;

			// Hex code to RGB Color
			$r = hexdec(substr($hex, 0, 2));
			$g = hexdec(substr($hex, 2, 2));
			$b = hexdec(substr($hex, 4, 2));

			// Get image color
			$imageColor = imagecolorallocate($canvas, $r, $g, $b);

			// Draw bar on canvas
			imagefilledrectangle($canvas, $x1, 0, $x2, $height, $imageColor);

			// Change current X point
			$currentX = $x2;
		}

		return $canvas;
	}

	/**
	 *	Break the code and print out error
	 *
	 *	@param string $type 		  	 Type of error
	 *	@param string $msg  			 Message to be displayed
	 *	@param boolean $disabledTrace To disable trace back error
	 */
	private function EBreak($type, $msg, $disableTrace = false) {
		
		// To trace back error
		$trace = debug_backtrace();
	    $trace = $trace[count($trace) - 1];

	    echo "<b>{$type}:</b> {$msg}";

	    if ($disableTrace === false) {
	    	echo " in <b>{$trace['file']}</b> on line <b>{$trace['line']}</b>";
	    }

	    echo "<br>";

	    die();
	}

	/**
	 *	Hex shift 1 bit left
	 *
	 *	@param string $hex Hexadecimal
	 *	@return string 1 bit left shifted Hexadecimal
	 */
	private function hexLeftBitShift($hex) {
		$hexInt = intval(hexdec($hex));
		$shiftInt = $hexInt << ColorbarCoder::SBIT;
		$shiftHex = dechex($shiftInt);

		// Add '0' in front of hex string if the length is less then size of hex code
		if (strlen($shiftHex) < ColorbarCoder::HEX_SIZE) {
			$zeros = str_repeat("0", ColorbarCoder::HEX_SIZE - strlen($shiftHex));
			$shiftHex = $zeros . $shiftHex;
		}

		return $shiftHex;
	}

	/**
	 *	Hex shift 1 bit right
	 *
	 *	@param string $hex Hexadecimal
	 *	@return string 1 bit right shifted Hexadecimal
	 */
	private function hexRightBitShift($hex) {
		$hexInt = intval(hexdec($hex));
		$shiftInt = $hexInt >> ColorbarCoder::SBIT;
		$shiftHex = dechex($shiftInt);

		// Add '0' in front of hex string if the length is less then size of hex code
		if (strlen($shiftHex) < ColorbarCoder::HEX_SIZE) {
			$zeros = str_repeat("0", ColorbarCoder::HEX_SIZE - strlen($shiftHex));
			$shiftHex = $zeros . $shiftHex;
		}

		return $shiftHex;
	}
}

?>