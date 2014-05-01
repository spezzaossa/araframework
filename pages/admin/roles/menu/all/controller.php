<?php
	class AllController extends BackController {

		public function execute() {
			if (Request::isAJAX())
			{
				$id	= intval($this->request->getParam('param_1'));

				$role = SysAdminRolesTable::getInstance()->find($id);
				$menus = $role->Menus;

				$ids = array();
				foreach($menus as $menu)
					$ids[] = $menu->id_menu;
				$ids = array_unique($ids);
				if (count($ids) == 0) $ids[] = 0;

				$all_menus = SysAdminMenusTable::getInstance()->createQuery('a')
						->where('id NOT IN ('.implode(',', $ids).')')
						->execute();
				$this->smarty->assign('all_menus', $all_menus);

				return $this->smarty->fetch(dirname(__FILE__)."/view.html");
			}

			// TODO: Far vedere pagina 404
		}
	}
?>