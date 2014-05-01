<?php
class SimpleImage {

	var $image;
	var $image_type;
	var $fill_width;
	var $fill_height;
	var $fill_color;

	function __construct($width = null, $height = null,
						array $color = array('r' => 255, 'g' => 255, 'b' => 255)) {
		if ($width && $height) {
			$this->fill_width = $width;
			$this->fill_height = $height;
			$this->fill_color = $color;
		}
	}

	function load($filename) {
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG ) {
			$this->image = imagecreatefromjpeg($filename);
		} elseif( $this->image_type == IMAGETYPE_GIF ) {
			$this->image = imagecreatefromgif($filename);
		} elseif( $this->image_type == IMAGETYPE_PNG ) {
			$this->image = imagecreatefrompng($filename);
		}
	}
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=80, $permissions=null) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image,$filename,$compression);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image,$filename);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image,$filename);
		}
		if( $permissions != null) {
			chmod($filename,$permissions);
		}
	}
	function output($image_type=IMAGETYPE_JPEG) {
		if( $image_type == IMAGETYPE_JPEG ) {
			imagejpeg($this->image);
		} elseif( $image_type == IMAGETYPE_GIF ) {
			imagegif($this->image);
		} elseif( $image_type == IMAGETYPE_PNG ) {
			imagepng($this->image);
		}
	}
	function getWidth() {
		return imagesx($this->image);
	}
	function getHeight() {
		return imagesy($this->image);
	}
	function resizeToHeight($height) {
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}

	function resizeToWidth($width) {
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}

	function scale($scale) {
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100;
		$this->resize($width,$height);
	}

	function resize($width, $height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		if ($this->fill_width && $this->fill_height) {
			$filled_image = imagecreatetruecolor($this->fill_width, $this->fill_height);
			$color = imagecolorallocate($filled_image, $this->fill_color['r'], $this->fill_color['g'], $this->fill_color['b']);
			imagefill($filled_image, 0, 0, $color);
			$dst_x = ($this->fill_width - $width) / 2;
			$dst_y = ($this->fill_height - $height) / 2;
			imagecopy($filled_image, $new_image, $dst_x, $dst_y, 0, 0, $width, $height);
			imagedestroy($new_image);
			$new_image = $filled_image;
		}
		$this->image = $new_image;
	}

	function resizeCrop($width, $height)
	{
		if ($this->getWidth() > $this->getHeight())
			$this->resizeToHeight($height);
		else
			$this->resizeToWidth($width);

		$src_width = $this->getWidth();
		$src_height = $this->getHeight();
		$src_x = round($src_width/2-$width/2);
		$src_y = round($src_height/2-$height/2);
		$dst_width = $width;
		$dst_height = $height;

		if($src_x < 0) {
			$dst_width = $src_width;
			$src_x = 0;
		}
		if($src_y < 0) {
			$dst_height = $src_height;
			$src_y = 0;
		}

		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, $src_x, $src_y, $dst_width, $dst_height, $dst_width, $dst_height);
		$this->image = $new_image;
	}

}
?>