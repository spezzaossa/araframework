<?php
	class UploadController extends BackController {

		public function execute() {
			$uploader = new qqFileUploader();
			$filename = $uploader->getName();

			$ext = explode('.', $filename);
			$ext = strtolower($ext[count($ext)-1]);

			switch($ext)
			{
				case 'jpg':
				case 'jpeg':
				case 'png':
				case 'gif':
					$type = 'Immagine'; break;
				case 'pdf':
					$type = 'PDF'; break;
				default:
					$type = 'Altro'; break;
			}
			
			$file = new SiteFiles();
			$file->type = $type;
			$file->created = date('Y-m-d H:i:s');

			$result = $uploader->handleUpload(MEDIA_FOLDER);
			if (isset($result['success'])){
				$file->filename = $result['filename'];
				$file->save();
				$file->generateThumbnail();
			}

			return json_encode($result);
		}
	}
?>