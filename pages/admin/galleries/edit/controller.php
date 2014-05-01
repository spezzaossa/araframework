<?php
	class EditController extends BackController
	{
		public function execute()
		{
			$id = $this->request->getParam('param_1');
			$gallery = SiteGalleriesTable::getInstance()->find($id);

			if(Request::isPOST())
			{
				$redirect = true;
				$params = $this->request->getParams();

				if (!$gallery)
				{
					$redirect = false;
					$gallery = new SiteGalleries();
				}
				$gallery->id_language = $params['lang'];
				$gallery->id_page = $params['ref_page'];
				$gallery->tag = $params['tag'];
				$gallery->name = $params['name'];
				$gallery->save();

				if ($redirect)
					header('Location: admin-galleries');
				else
					header('Location: admin-galleries-edit-'.$gallery->id);
				exit;
			}

			$this->smarty->assign('languages', SysLanguagesTable::getInstance()->createQuery()->fetchArray());
			$this->smarty->assign('pages', SysPagesTable::getInstance()->createQuery()->execute());
			$this->smarty->assign('gallery', $gallery);
		}
	}
?>