<?php
	class EditController extends BackController {

		public function execute() {

			if (Request::isPOST()) {
				$params = $this->request->getParams();
				$slug = SysSlugsTable::getInstance()->find($params['id']);

				if ($params['slug'] && $params['page_url'])
				{
					if (!$slug) $slug = new SysSlugs();

					$slug->slug = $params['slug'];
					$slug->page_url = $params['page_url'];
					$slug->save();
				}

				header('Location: admin-slugs');
				exit;
			}
		}
	}
?>