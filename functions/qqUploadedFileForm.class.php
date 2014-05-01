<?php
/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
	/**
		* Save the file to the specified path
		* @return boolean TRUE on success
		*/
	function save($dir, $file, $ext, array $maxImageSize  = array('w' => 0, 'h' => 0), array $minImageSize = array('w' => 0, 'h' => 0)) {
		list($width, $height) = getimagesize($_FILES['qqfile']['tmp_name']);
		if (($maxImageSize['w'] > 0 && $width > $maxImageSize['w']) || ($maxImageSize['h'] > 0 &&  $height > $maxImageSize['h']) ||
			($minImageSize['w'] > 0 && $width < $minImageSize['w']) || ($minImageSize['h'] > 0 && $height < $minImageSize['w']))
			return false;

		$path = $dir . $file . '.' . $ext;
		if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
			return false;
		}
		return true;
	}
	function getName() {
		return $_FILES['qqfile']['name'];
	}
	function getSize() {
		return $_FILES['qqfile']['size'];
	}
}
?>