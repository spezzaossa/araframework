<?php
	class GetController extends BackController {

		public function execute() {
			if (Request::isAJAX())
			{
				$id	= intval($this->request->getParam('param_1'));

				$role = SysAdminRolesTable::getInstance()->find($id);
				$menu = $role->Menus;

				$this->smarty->assign('role', $role);
				$this->smarty->assign('menus', $menu);

				return $this->smarty->fetch(dirname(__FILE__)."/view.html");
			}

			// TODO: Far vedere pagina 404
		}
	}
?>