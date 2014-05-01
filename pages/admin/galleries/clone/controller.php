<?php
	class CloneController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$gallery = SiteGalleriesTable::getInstance()->find($id);

			if ($gallery)
			{
				if(Request::isPOST())
				{
					$params = $this->request->getParams();

					$new = new SiteGalleries();
					$new->name = $params['name'];
					$new->id_language = $params['lang'];
					$new->id_page = $params['ref_page'];
					$new->tag = $params['tag'];
					$new->save();

					foreach($gallery->Images as $image)
					{
						$new_image = new SiteGalleriesImages();
						$new_image->id_gallery = $new->id;
						$new_image->id_image = $image->id;
						$new_image->save();
					}

					header('Location: admin-galleries'); exit;
				}
				else
					$this->smarty->assign('original', $gallery);
			}

			$this->smarty->assign('languages', SysLanguagesTable::getInstance()->createQuery()->fetchArray());
			$this->smarty->assign('pages', SysPagesTable::getInstance()->createQuery()->execute());
		}
	}
?>