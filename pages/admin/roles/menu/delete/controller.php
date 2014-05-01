<?php
	class DeleteController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				$id			= intval($this->request->getParam('param_1'));
				$menu_id	= $this->request->getParam('menu');

				$menu = SysRolesMenusTable::getInstance()->createQuery('a')
						->where('id_role = ?', $id)
						->andWhere('id = ?', $menu_id)
						->fetchOne();

				foreach($menu->Children as $child)
					$child->delete();

				$menu->delete();

				return '1';
			}
		}
	}
?>