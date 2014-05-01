<?php
	class CheckController extends BackController {

		public function execute() {

			if(Request::isAJAX()) {
				$slug = $this->request->getParam('slug');
				$id = intval($this->request->getParam('id'));

				$exists = SysSlugsTable::getInstance()->createQuery('a')
					->where("slug LIKE '$slug'");

				if ($id) $exists->andWhere("id <> $id");

				$exists = $exists->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

				return ($exists) ? '-1' : '1';
			}
		}
	}
?>