<?php

namespace Poundation;

use Poundation\Server\PConfig;
use Poundation\PString;

class PImage extends PObject
{

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
	static function createImageFromString($data, $name)
	{
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
	static function hashFromFilename($filename)
	{
		$hash = null;

		if (file_exists($filename)) {
			$hash = md5_file($filename);
		}

		return $hash;
	}

	private function setImage($image)
	{
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
	static function createImageFromFilename($filename, $name = false)
	{
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
	static function createFromURL(\Poundation\PURL $url, $filename = null)
	{

		$image = null;

		if (PConfig::isCurlEnabled()) {

			$curl = curl_init((string)$url);

			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);

			$binary       = curl_exec($curl);
			$statusCode   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$contentType  = __(curl_getinfo($curl, CURLINFO_CONTENT_TYPE));
			$effectiveURL = PURL::URLWithString(curl_getinfo($curl, CURLINFO_EFFECTIVE_URL));

			curl_close($curl);

			if ($statusCode == 200) {
				if (strlen($binary) > 0) {

					if (is_null($filename)) {
						$filename = null;
						// filename determination
						if ($effectiveURL) {
							$pathComponents = $effectiveURL->pathComponents();
							if ($pathComponents) {
								$filename = $pathComponents->lastObject();
							}
						}

						// if the URL does not contain a filename we generate a unique one
						if (is_null($filename)) {
							$filename = (string)PString::createUUID();
						}

						// checking the mime type
						$MIME = __(PMIME::getTypeForFilename($filename));
						if (!$MIME->hasPrefix('image')) {
							// since the filename indicates no image we check the header
							if ($contentType->hasPrefix('image')) {
								$MIME = $contentType;
							}

							// concating the filename and the extension
							$filename .= '.' . PMIME::getExtensionForType($MIME);
						}
					}

					if (!is_null($filename)) {
						// finally, create the image
						$image = self::createImageFromString($binary, $filename);
					}
				}
			} else {
				throw new \Exception('Received HTTP status ' . $statusCode);
			}

		} else {
			throw new \Exception('cURL extension missing.', 500);
		}

		return $image;

	}


	private function importString($string)
	{
		$this->image = imagecreatefromstring($string);

		return (is_resource($this->image));
	}

	public function getLength()
	{
		return strlen($this->getData());
	}

	/**
	 * Returns the raw image data. Can be used for saving.
	 *
	 * @return string
	 */
	public function getData()
	{
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
	public function getHash()
	{
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
	public function getName()
	{
		return $this->name;
	}

	public function getExtension()
	{
		return strtolower(pathinfo($this->getName(), PATHINFO_EXTENSION));
	}

	public function setName($name)
	{
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
	public function getWidth()
	{
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
	public function getHeight()
	{
		if (is_resource($this->image)) {
			return imagesy($this->image);
		}

		return 0;
	}

	public function resize($newWidth, $newHeight, $option = self::RESIZE_AUTO)
	{

		// Get optimal width and height - based on $option
		$optionArray = $this->getDimensions($newWidth, $newHeight, strtolower($option));

		$optimalWidth  = round($optionArray['optimalWidth'], 0);
		$optimalHeight = round($optionArray['optimalHeight'], 0);

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

	private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
	{
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

	private function getDimensions($newWidth, $newHeight, $option)
	{

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
		return array(
			'optimalWidth'  => $optimalWidth,
			'optimalHeight' => $optimalHeight
		);
	}

	private function getSizeByFixedHeight($newHeight)
	{
		$ratio    = $this->getWidth() / $this->getHeight();
		$newWidth = $newHeight * $ratio;

		return $newWidth;
	}

	private function getSizeByFixedWidth($newWidth)
	{
		$ratio     = $this->getHeight() / $this->getWidth();
		$newHeight = $newWidth * $ratio;

		return $newHeight;
	}

	private function getSizeByAuto($newWidth, $newHeight)
	{
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

		return array(
			'optimalWidth'  => $optimalWidth,
			'optimalHeight' => $optimalHeight
		);
	}

	private function getOptimalCrop($newWidth, $newHeight)
	{

		$heightRatio = $this->getHeight() / $newHeight;
		$widthRatio  = $this->getWidth() / $newWidth;

		if ($heightRatio < $widthRatio) {
			$optimalRatio = $heightRatio;
		} else {
			$optimalRatio = $widthRatio;
		}

		$optimalHeight = $this->getHeight() / $optimalRatio;
		$optimalWidth  = $this->getWidth() / $optimalRatio;

		return array(
			'optimalWidth'  => $optimalWidth,
			'optimalHeight' => $optimalHeight
		);
	}

	public function isPNG()
	{
		return ($this->getMIME() == 'image/png');
	}

	public function isJPG()
	{
		return ($this->getMIME() == 'image/jpeg');
	}

	public function getMIME()
	{
		return PMIME::getTypeForExtension($this->getName());
	}

}
