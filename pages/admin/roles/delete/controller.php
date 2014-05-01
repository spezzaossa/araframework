<?php
	class DeleteController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				$id		= intval($this->request->getParam('param_1'));

				$menus = SysRolesMenusTable::getInstance()->createQuery('a')
						->where('id_role = ?', $id)
						->execute()
						;
				foreach ($menus as $menu)
					$menu->delete();

				$admins = SysAdminsTable::getInstance()->createQuery('a')
						->where('id_role = ?', $id)
						->execute()
						;
				foreach ($admins as $admin)
				{
					$admin->id_role = 0;
					$admin->save();
				}

				$role = SysAdminRolesTable::getInstance()->find($id);
				$role->delete();

				return '1';
			}
		}
	}
?>