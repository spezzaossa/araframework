<?php
	class CropController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$file = SiteFilesTable::getInstance()->find($id);
			list($width, $height, $type) = getimagesize(MEDIA_FOLDER . $file->filename);

			if (Request::isPOST())
			{
				$wid 	= $this->request->getParam('wid');
				$hei 	= $this->request->getParam('hei');
				$x1 	= $this->request->getParam('x1');
				$y1 	= $this->request->getParam('y1');

				switch($type)
				{
					case IMAGETYPE_GIF:	$source = imagecreatefromgif(MEDIA_FOLDER . $file->filename); break;
					case IMAGETYPE_PNG: $source = imagecreatefrompng(MEDIA_FOLDER . $file->filename); break;
					case IMAGETYPE_JPEG: $source = imagecreatefromjpeg(MEDIA_FOLDER . $file->filename); break;
				}

				$dest = imagecreatetruecolor($wid, $hei);
				imagecopyresampled($dest, $source, 0, 0, $x1, $y1, $wid, $hei, $wid, $hei);

				switch($type)
				{
					case IMAGETYPE_GIF:	imagegif($dest, MEDIA_FOLDER . $file->filename); break;
					case IMAGETYPE_PNG: imagepng($dest, MEDIA_FOLDER . $file->filename); break;
					case IMAGETYPE_JPEG: imagejpeg($dest, MEDIA_FOLDER . $file->filename, 80); break;
				}

				$file->generateThumbnail();
			}

			list($width, $height) = getimagesize(MEDIA_FOLDER . $file->filename);
			$this->smarty->assign('width', $width);
			$this->smarty->assign('height', $height);
			$this->smarty->assign('file', $file);
		}
	}
?>