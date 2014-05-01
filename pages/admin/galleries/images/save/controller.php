<?php
	class SaveController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$images = $this->request->getParam('images');

			$gallery = SiteGalleriesTable::getInstance()->find($id);

			if ($gallery)
			{
				SiteGalleriesImagesTable::getInstance()->createQuery('a')
					->delete()
					->where('id_gallery = ?', $id)
					->execute();

				$count = 1;
				foreach($images as $image)
				{
					$new = new SiteGalleriesImages();
					$new->id_gallery = $id;
					$new->id_image = $image;
					$new->sort_order = $count++;
					$new->save();
				}

				return "1";
			}

			return "-1";
		}
	}
?>