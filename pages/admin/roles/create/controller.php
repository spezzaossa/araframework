<?php
	class CreateController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {

				$name	= $this->request->getParam('name');
				if ($name)
				{
					$role = new SysAdminRoles();
					$role->name = $name;
					$role->save();
					return '1';
				}

				return '0';
			}
		}
	}
?>