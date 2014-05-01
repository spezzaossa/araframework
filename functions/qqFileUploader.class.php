<?php
class qqFileUploader {
	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $maxImageSize = array('w' => 0, 'h' => 0);
	private $minImageSize = array('w' => 0, 'h' => 0);
	private $file;

	function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760,
			array $maxImageSize  = array('w' => 0, 'h' => 0), array $minImageSize = array('w' => 0, 'h' => 0)){
		$allowedExtensions = array_map("strtolower", $allowedExtensions);

		$this->allowedExtensions = $allowedExtensions;
		$this->sizeLimit = $sizeLimit;
		$this->maxImageSize = ($maxImageSize == null) ? array('w' => 0, 'h' => 0) : $maxImageSize;
		$this->minImageSize = ($minImageSize == null) ? array('w' => 0, 'h' => 0) : $minImageSize;

		$this->checkServerSettings();

		if (isset($_GET['qqfile'])) {
			$this->file = new qqUploadedFileXhr();
		} elseif (isset($_FILES['qqfile'])) {
			$this->file = new qqUploadedFileForm();
		} else {
			$this->file = false;
		}
	}

	public function getName(){
		if ($this->file)
			return $this->file->getName();
	}

	private function checkServerSettings(){
		$postSize = $this->toBytes(ini_get('post_max_size'));
		$uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

		if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
			$size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
			die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
		}
	}

	private function toBytes($str){
		$val = trim($str);
		$last = strtolower($str[strlen($str)-1]);
		switch($last) {
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
		* Returns array('success'=>true) or array('error'=>'error message')
		*/
	function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
		if (!is_writable($uploadDirectory)){
			return array('error' => "Server error. Upload directory isn't writable.");
		}

		if (!$this->file){
			return array('error' => 'No files were uploaded.');
		}

		$size = $this->file->getSize();

		if ($size == 0) {
			return array('error' => 'File is empty');
		}

		if ($size > $this->sizeLimit) {
			return array('error' => 'File is too large');
		}

		$pathinfo = pathinfo($this->file->getName());
		$filename = $pathinfo['filename'];
		$ext = @$pathinfo['extension'];		// hide notices if extension is empty

		if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
			$these = implode(', ', $this->allowedExtensions);
			return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
		}

		if(!$replaceOldFile){
			/// don't overwrite previous files that were uploaded
			while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
				$filename .= rand(10, 99);
			}
		}

		// Modified to allow thumb of multiple dimensions by having the filename
		// divided from its extension
		if ($this->file->save($uploadDirectory, $filename, $ext, $this->maxImageSize, $this->minImageSize)){
			return array('success'=>true, 'filename' => $filename.'.'.$ext);
		} else {
			return array('error'=> 'Could not save uploaded file.' .
					'The upload was cancelled, the file dimensions do not respect constraints or server error encountered');
		}

	}
}
?>