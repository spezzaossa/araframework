<?php
	class EditController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$lang = $this->request->getParam('lang');

			$file = SiteFilesTable::getInstance()->find($id);
			$meta = SiteFilesMetaTable::getInstance()->createQuery('a')
				->where('id_file = ?', $id)
				->andWhere('id_language = ?', $lang)
				->fetchOne()
			;

			if ($file && Request::isAJAX())
			{
				$alt = $this->request->getParam('alt');
				$title = $this->request->getParam('title');

				if (!$meta) $meta = new SiteFilesMeta();
				$meta->alt = $alt;
				$meta->title = $title;
				$meta->id_language = $lang;
				$meta->id_file = $id;
				$meta->save();

				return '1';
			}
		}
	}
?>