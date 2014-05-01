<?php
	class DeleteController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$file = SiteFilesTable::getInstance()->find($id);
			if ($file)
			{
				foreach($file->Meta as $meta)
					$meta->delete();

				@unlink(MEDIA_FOLDER.$file->filename);
				@unlink(MEDIA_FOLDER.'th_'.$file->filename);
				$file->delete();
			}

			return '1';
		}
	}
?>