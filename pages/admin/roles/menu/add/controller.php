<?php
	class AddController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				$id		= intval($this->request->getParam('param_1'));

				$element = $this->request->getParam('element');
				$parent = $this->request->getParam('parent');

				if ($element)
				{
					if ($parent)
					{
						$menus = SysRolesMenusTable::getInstance()->createQuery('a')
								->select('a.id, MAX(sort_order) AS sort_order')
								->where('id_role = ?', $id)
								->andWhere('id_parent = ?', $parent)
								->fetchOne();
						;

						$menu = new SysRolesMenus();
						$menu->id_role = $id;
						$menu->id_menu = $element;
						$menu->id_parent = $parent;
						$menu->sort_order = $menus->sort_order + 1;
						$menu->save();
					}
					else
					{
						$menus = SysRolesMenusTable::getInstance()->createQuery('a')
								->select('MAX(sort_order) AS sort_order')
								->where('id_role = ?', $id)
								->andWhere('id_parent = 0')
								->fetchOne();
						;

						$menu = new SysRolesMenus();
						$menu->id_role = $id;
						$menu->id_menu = $element;
						$menu->id_parent = 0;
						$menu->sort_order = $menus->sort_order + 1;
						$menu->save();
					}
				}

				return '1';
			}
		}
	}
?>