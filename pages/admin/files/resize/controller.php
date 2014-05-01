<?php
	class ResizeController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$file = SiteFilesTable::getInstance()->find($id);
			list($width, $height) = getimagesize(MEDIA_FOLDER . $file->filename);

			if (Request::isPOST())
			{
				$new_width = $this->request->getParam('width');
				$new_height = $this->request->getParam('height');
				$c = $this->request->getParam('color');

				switch($c)
				{
					case 'black':	$color = array('r' => 0, 'g' => 0, 'b' => 0); break;
					case 'white':
					default:		$color = array('r' => 255, 'g' => 255, 'b' => 255); break;
				}
				$img = new SimpleImage($new_width, $new_height, $color);
				$img->load(MEDIA_FOLDER . $file->filename);

				$ratio = $width / $height;
				$new_ratio = $new_width / $new_height;
				$d_ratio = $ratio / $new_ratio;

				if ($d_ratio == 1)
					$img->resize($new_width, $new_height);
				else
				{
					/* VERSIONE CON CORNICE */
					/*
					if ($d_ratio > 1)
						$img->resizeToWidth($new_width);
					else
						$img->resizeToHeight($new_height);
					//*/

					/* VERSIONE SENZA CORNICE */
					//*
					if ($d_ratio > 1)
						$img->resizeToHeight($new_height);
					else
						$img->resizeToWidth($new_width);
					//*/
				}

				$img->save(MEDIA_FOLDER . $file->filename, $img->image_type);
				$img->generateThumbnail();
				unset($width, $height);
			}

			list($width, $height) = getimagesize(MEDIA_FOLDER . $file->filename);
			$this->smarty->assign('width', $width);
			$this->smarty->assign('height', $height);
			$this->smarty->assign('file', $file);
		}
	}
?>