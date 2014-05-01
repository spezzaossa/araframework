<?php
	class CreateController extends BackController {

		public function execute() {
			if(Request::isAJAX())
			{
				$name		= $this->request->getParam('name');
				$password	= $this->request->getParam('password');
				$role		= $this->request->getParam('role');

				if ($name)
				{
					$user = new SysAdmins();
					$user->username = $name;
					$user->password = hash('sha512', $password);
					$user->id_role = $role;
					$user->save();
					return '1';
				}
				return '0';
			}
		}
	}
?>