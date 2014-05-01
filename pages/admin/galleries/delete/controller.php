<?php
	class DeleteController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$gallery = SiteGalleriesTable::getInstance()->find($id);
			if ($gallery)
			{
				SiteGalleriesImagesTable::getInstance()->createQuery('a')
					->delete()
					->where('id_gallery = ?', $gallery->id)
					->execute()
				;

				$gallery->delete();
			}

			return '1';
		}
	}
?>