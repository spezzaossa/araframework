<?php
	class CloneController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$file = SiteFilesTable::getInstance()->find($id);
			if ($file)
			{
				$pathinfo = pathinfo(MEDIA_FOLDER . $file->filename);
				$filename = $pathinfo['filename'];
				$ext = @$pathinfo['extension'];		// hide notices if extension is empty

				$filename .= rand(10, 99);
				while (file_exists(MEDIA_FOLDER . $filename)) {
					$filename .= rand(10, 99);
				}

				$new = new SiteFiles();
				$new->filename = $filename.'.'.$ext;
				$new->type = $file->type;
				$new->alt = $file->alt;
				$new->title = $file->title;
				$new->created = date('Y-m-d H:i:s');

				if (copy(MEDIA_FOLDER . $file->filename, MEDIA_FOLDER . $new->filename))
				{
					$new->save();
					$new->generateThumbnail();
				}
				else
					return '-1';
			}

			return '1';
		}
	}
?>