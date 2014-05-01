<?php
	class MenuController extends BackController {

		public function execute() {
			$id	= intval($this->request->getParam('param_1'));

			$role = SysAdminRolesTable::getInstance()->find($id);
			$menu = $role->Menus;

			$this->smarty->assign('role', $role);
			$this->smarty->assign('menus', $menu);

		}
	}
?>