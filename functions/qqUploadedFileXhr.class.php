<?php
/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
	/**
		* Save the file to the specified path
		* @return boolean TRUE on success
		*/
	function save($dir, $file, $ext, $maxImageSize  = array('w' => 0, 'h' => 0), $minImageSize = array('w' => 0, 'h' => 0)) {
		$path = $dir . $file . '.' . $ext;
		$input = fopen("php://input", "r");
		$temp = tmpfile();
		$realSize = stream_copy_to_stream($input, $temp);
		fclose($input);

		$metaDatas = stream_get_meta_data($temp);
		$tmpFilename = $metaDatas['uri'];

		list($width, $height) = getimagesize($tmpFilename);
		if (($maxImageSize['w'] > 0 && $width > $maxImageSize['w']) || ($maxImageSize['h'] > 0 &&  $height > $maxImageSize['h']) ||
			($minImageSize['w'] > 0 && $width < $minImageSize['w']) || ($minImageSize['h'] > 0 && $height < $minImageSize['w']))
			return false;

		/**
		 * Fix per IE dell'upload asincrono della versione 3.0 di FineUploader
		 */
		if (!$realSize)
		{
			// IE ?
			list($width, $height) = getimagesize($_FILES['qqfile']['tmp_name']);
			if (($maxImageSize['w'] > 0 && $width > $maxImageSize['w']) || ($maxImageSize['h'] > 0 &&  $height > $maxImageSize['h']) ||
				($minImageSize['w'] > 0 && $width < $minImageSize['w']) || ($minImageSize['h'] > 0 && $height < $minImageSize['w']))
				return false;

			$success = move_uploaded_file($_FILES['qqfile']['tmp_name'], $path);
			if (!$success)
				return false;
		} elseif ($realSize != $this->getSize()){
			return false;
		} else {
			$target = fopen($path, "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}

		$target = fopen($path, "w");
		fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
		fclose($target);

		/* Resize image */
//		$image = new SimpleImage(700, 525);
//		$image->load($path);
//
//		$thumb = new SimpleImage(200, 150);
//		$thumb->load($path);
//		if ($image->getHeight() > $image->getWidth()) {
//			$image->resizeToHeight(525);
//			$image->save($path);
//
//			$thumb->resizeToHeight(150);
//			$thumb->save($dir . 'thumbs/' . $file . '.' . $ext);
//		}
//		else {
//			$image->resizeToWidth(700);
//			$image->save($path);
//
//			$thumb->resizeToWidth(200);
//			$thumb->save($dir . 'thumbs/' . $file . '.' . $ext);
//		}
		/* End resize */

		return true;
	}
	function getName() {
		return $_GET['qqfile'];
	}
	function getSize() {
		if (isset($_SERVER["CONTENT_LENGTH"])){
			return (int)$_SERVER["CONTENT_LENGTH"];
		} else {
			throw new Exception('Getting content length is not supported.');
		}
	}
}
?>