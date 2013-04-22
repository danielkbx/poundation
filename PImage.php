<?php

namespace Poundation;

use Poundation\Server\PConfig;
use Poundation\PString;

class PImage extends PObject {

	const RESIZE_AUTO      = 'auto';
	const RESIZE_EXACT     = 'exact';
	const RESIZE_BY_WIDTH  = 'landscape';
	const RESIZE_BY_HEIGHT = 'portrait';
	const RESIZE_CROP      = 'crop';

	private $name;

	private $data;
	private $hash;

	private $image = null;

	private $imageResized;

	/**
	 * Creates a new image from the given data.
	 *
	 * @param $data
	 * @param $name The filename of the string. It is used to determine the MIME type.
	 *
	 * @return null|PImage
	 */
	static function createImageFromString($data, $name) {
		$image = null;
		if (is_string($data) && strlen($data) > 0) {

			$image = new PImage();
			if ($image->importString($data) === false) {
				$image = null;
			} else {
				$image->name = $name;
				$image->data = $data;
			}

		}

		return $image;
	}

	/**
	 * Returns the hash of an image file without loading it into memory.
	 *
	 * @param $filename
	 *
	 * @return null|string
	 */
	static function hashFromFilename($filename) {
		$hash = null;

		if (file_exists($filename)) {
			$hash = md5_file($filename);
		}

		return $hash;
	}

	private function setImage($image) {
		if (is_resource($image)) {
			$this->image = $image;
			$this->data  = null;
			$this->hash  = null;
		}
	}

	/**
	 * Creates an image from the file.
	 *
	 * @param        $filename
	 * @param string $name
	 *
	 * @return null|PImage
	 */
	static function createImageFromFilename($filename, $name = false) {
		$image = null;

		if (file_exists($filename)) {
			$fileHandle = fopen($filename, 'r');
			if ($fileHandle) {
				$fileInfo = fstat($fileHandle);
				$fileSize = (isset($fileInfo['size']) ? (int)$fileInfo['size'] : 0);

				if ($fileSize > 0) {
					$binary = fread($fileHandle, $fileSize);
					if ($name === false) {
						$name  = basename($filename);
						$image = self::createImageFromString($binary, $name);
					}
				}
			}
		}

		return $image;
	}

	/**
	 * Creates an image from a given URL
	 *
	 * @param PURL $url
	 */
	static function createFromUrl(\Poundation\PURL $url, $filename = false) {
		if (!$url instanceof PURL) {
			return null;
		}

		$hasExtension = false;
		$mimeType     = null;
		$extension    = null;
		$data         = null;
		$image        = null;

		if ($filename === false) {

			$pathInfo = pathinfo($url->path());
			if (isset($pathInfo['extension']) && ($pathInfo['extension'] == 'png' || $pathInfo['extension'] == 'gif' || $pathInfo['extension'] == 'jpg' || $pathInfo['extension'] == 'jpeg')
			) {
				// seems to be an image file
				$filename = $pathInfo['filename'] . '.' . $pathInfo['extension'];

				$extension = $pathInfo['extension'];

			} else {
				$filename = PString::createUUID();
			}
		}

		if (PConfig::curlEnabled()) {

			$ch = curl_init($url);

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, true);

			$result = curl_exec($ch);
			list($headers, $data) = explode("\r\n\r\n", $result, 2);

			$header = curl_getinfo($ch);
			curl_close($ch);

			if (isset($header['content_type'])) {
				$mimeType = $header['content_type'];
			}
			;

		} else {
			if (PConfig::allowURLFOpen()) {
				$data = file_get_contents($url);
			} else {
				throw new Exception('Server not able to fetch image. Check php.ini for allow_url_fopen or curl', 500);
			}
		}

		if (is_null($mimeType) && is_null($extension)) {

			// We need to safe the image temporary to get the mime type
			$img = fopen('/tmp/' . $filename, 'rw');
			fwrite($img, $data);
			fclose($img);
			$imageData = getImageSize($img);
			if (!$imageData) {
				return null;
			}
			$mimeType = $imageData['mime'];

		}

		if (is_null($extension) && !is_null($mimeType)) {
			// create extension from mime-type
			$extension = self::getExtensionForMimeType($mimeType);
			$filename  = $filename . '.' . $extension;
		}

		$image = @self::createImageFromString($data, $filename);

		return $image;

	}


	private function importString($string) {
		$this->image = imagecreatefromstring($string);

		return (is_resource($this->image));
	}

	public function getLength() {
		return strlen($this->getData());
	}

	/**
	 * Returns the raw image data. Can be used for saving.
	 *
	 * @return string
	 */
	public function getData() {
		if ($this->data == null && is_resource($this->image)) {
			ob_start();
			$mime = $this->getMIME();

			if ($this->isPNG()) {
				imagepng($this->image, null, 4);
			} else {
				imagejpeg($this->image, null, 90);
			}

			$this->data = ob_get_contents();
			ob_end_clean();
		}

		return $this->data;
	}

	/**
	 * Returns a MD5 hash of the image binary data.
	 *
	 * @return string
	 */
	public function getHash() {
		if ($this->hash == null) {
			$this->hash = md5($this->getData());
		}

		return $this->hash;
	}

	/**
	 * Returns the name of the image.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	public function getExtension() {
		return strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
	}

	public function setName($name) {
		$extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		if ($extension !== $this->getExtension()) {
			_logger()->warn('Setting a new name (' . $name . ') on a content image (' . $this->getName() . ') but the extension does not match.');
		}
		$this->name = $name;
	}

	/**
	 * Returns the width of the image.
	 *
	 * @return int
	 */
	public function getWidth() {
		if (is_resource($this->image)) {
			return imagesx($this->image);
		}

		return 0;
	}

	/**
	 * Returns the height of the image.
	 *
	 * @return int
	 */
	public function getHeight() {
		if (is_resource($this->image)) {
			return imagesy($this->image);
		}

		return 0;
	}

	public function resize($newWidth, $newHeight, $option = self::RESIZE_AUTO) {

		// Get optimal width and height - based on $option
		$optionArray = $this->getDimensions($newWidth, $newHeight, strtolower($option));

		$optimalWidth  = round($optionArray['optimalWidth'],0);
		$optimalHeight = round($optionArray['optimalHeight'],0);

		// Resample - create image canvas of x, y size
		$this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
		if ($this->isPNG()) {
			imagealphablending($this->imageResized, false);
			imagesavealpha($this->imageResized, true);
		}
		imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->getWidth(), $this->getHeight());

		// if option is 'crop', then crop too
		if ($option == self::RESIZE_CROP) {
			if ($newWidth != $optimalWidth || $newHeight != $optimalHeight) {
				$this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
			}
		}

		$success = (!is_null($this->imageResized));

		$this->setImage($this->imageResized);
		$this->imageResized = null;

		return $success;
	}

	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight) {
		// Find center - this will be used for the crop
		$cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
		$cropStartY = ($optimalHeight / 2) - ($newHeight / 2);

		$crop = $this->imageResized;

		// *** Now crop from center to exact requested size
		$this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
		if ($this->isPNG()) {
			imagealphablending($this->imageResized, false);
			imagesavealpha($this->imageResized, true);
		}
		imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth, $newHeight);
	}

	private function getDimensions($newWidth, $newHeight, $option) {

		switch ($option) {
			case self::RESIZE_EXACT:
				$optimalWidth  = $newWidth;
				$optimalHeight = $newHeight;
				break;
			case self::RESIZE_BY_HEIGHT:
				$optimalWidth  = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight = $newHeight;
				break;
			case self::RESIZE_BY_WIDTH:
				$optimalWidth  = $newWidth;
				$optimalHeight = $this->getSizeByFixedWidth($newWidth);
				break;
			case self::RESIZE_AUTO:
				$optionArray   = $this->getSizeByAuto($newWidth, $newHeight);
				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
			case self::RESIZE_CROP:
				$optionArray   = $this->getOptimalCrop($newWidth, $newHeight);
				$optimalWidth  = $optionArray['optimalWidth'];
				$optimalHeight = $optionArray['optimalHeight'];
				break;
		}
		return array('optimalWidth'  => $optimalWidth,
					 'optimalHeight' => $optimalHeight
		);
	}

	private function getSizeByFixedHeight($newHeight) {
		$ratio    = $this->getWidth() / $this->getHeight();
		$newWidth = $newHeight * $ratio;

		return $newWidth;
	}

	private function getSizeByFixedWidth($newWidth) {
		$ratio     = $this->getHeight() / $this->getWidth();
		$newHeight = $newWidth * $ratio;

		return $newHeight;
	}

	private function getSizeByAuto($newWidth, $newHeight) {
		if ($this->getHeight() < $this->getWidth()) {
			// Image to be resized is wider (landscape)
			$optimalWidth  = $newWidth;
			$optimalHeight = $this->getSizeByFixedWidth($newWidth);
		} elseif ($this->getHeight() > $this->getWidth()) {
			// Image to be resized is taller (portrait)
			$optimalWidth  = $this->getSizeByFixedHeight($newHeight);
			$optimalHeight = $newHeight;
		} else {
			// Image to be resizerd is a square
			if ($newHeight < $newWidth) {
				$optimalWidth  = $newWidth;
				$optimalHeight = $this->getSizeByFixedWidth($newWidth);
			} else if ($newHeight > $newWidth) {
				$optimalWidth  = $this->getSizeByFixedHeight($newHeight);
				$optimalHeight = $newHeight;
			} else {
				// *** Sqaure being resized to a square
				$optimalWidth  = $newWidth;
				$optimalHeight = $newHeight;
			}
		}

		return array('optimalWidth'  => $optimalWidth,
					 'optimalHeight' => $optimalHeight
		);
	}

	private function getOptimalCrop($newWidth, $newHeight) {

		$heightRatio = $this->getHeight() / $newHeight;
		$widthRatio  = $this->getWidth() / $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->getHeight() / $optimalRatio;
		$optimalWidth  = $this->getWidth() / $optimalRatio;

		return array('optimalWidth'  => $optimalWidth,
					 'optimalHeight' => $optimalHeight
		);
	}

	public function isPNG() {
		return ($this->getMIME() == 'image/png');
	}

	public function isJPG() {
		return ($this->getMIME() == 'image/jpeg');
	}

	/**
	 * Returns the MIME type of the image.
	 *
	 * @return string
	 */
	public function getMIME() {

		$mime = "application/octet-stream";

		$extension = $this->getExtension();

		if (is_string($this->name)) {

			switch ($extension) {
				case "bmp":
					$mime = "image/bmp";
					break;
				case "gif":
					$mime = "image/gif";
					break;
				case "ief":
					$mime = "image/ief";
					break;
				case "jpeg":
					$mime = "image/jpeg";
					break;
				case "jpg":
					$mime = "image/jpeg";
					break;
				case "jpe":
					$mime = "image/jpeg";
					break;
				case "png":
					$mime = "image/png";
					break;
				case "tiff":
					$mime = "image/tiff";
					break;
				case "tif":
					$mime = "image/tiff";
					break;
				case "djvu":
					$mime = "image/vnd.djvu";
					break;
				case "djv":
					$mime = "image/vnd.djvu";
					break;
				case "wbmp":
					$mime = "image/vnd.wap.wbmp";
					break;
				case "ras":
					$mime = "image/x-cmu-raster";
					break;
				case "pnm":
					$mime = "image/x-portable-anymap";
					break;
				case "pbm":
					$mime = "image/x-portable-bitmap";
					break;
				case "pgm":
					$mime = "image/x-portable-graymap";
					break;
				case "ppm":
					$mime = "image/x-portable-pixmap";
					break;
				case "rgb":
					$mime = "image/x-rgb";
					break;
				case "xbm":
					$mime = "image/x-xbitmap";
					break;
				case "xpm":
					$mime = "image/x-xpixmap";
					break;
				case "xwd":
					$mime = "image/x-xwindowdump";
					break;
			}
		}

		return $mime;
	}

	static function getExtensionForMimeType($mime) {

		switch ($mime) {
			case "image/bmp":
				$extension = "bmp";
				break;
			case "image/gif":
				$extension = "gif";
				break;
			case "image/ief":
				$extension = "ief";
				break;
			case "image/jpeg":
				$extension = "jpeg";
				break;
			case "image/jpg":
				$extension = "jpeg";
				break;
			case "image/jpe":
				$extension = "jpeg";
				break;
			case "image/png":
				$extension = "png";
				break;
			case "image/tiff":
				$extension = "tiff";
				break;
			case "image/tiff":
				$extension = "tiff";
				break;
			case "image/vnd.djvu":
				$extension = "djvu";
				break;
			case "image/vnd.djv":
				$extension = "djv";
				break;
			case "image/vnd.wap.wbmp":
				$extension = "wbmp";
				break;
			case "image/x-cmu-raster":
				$extension = "ras";
				break;
			case "image/x-portable-anymap":
				$extension = "pnm";
				break;
			case "image/x-portable-bitmap":
				$extension = "pbm";
				break;
			case "image/x-portable-graymap":
				$extension = "pgm";
				break;
			case "image/x-portable-pixmap":
				$extension = "ppm";
				break;
			case "image/x-rgb":
				$extension = "rgb";
				break;
			case "image/x-xbitmap":
				$extension = "xbm";
				break;
			case "image/x-xpixmap":
				$extension = "xpm";
				break;
			case "image/x-xwindowdump":
				$extension = "xwd";
				break;
			default:
				$extension = null;
		}
		return $extension;
	}
}
