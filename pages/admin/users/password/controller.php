<?php
	class PasswordController extends BackController {

		public function execute() {
			if(Request::isAJAX())
			{
				$id			= intval($this->request->getParam('param_1'));
				$password	= $this->request->getParam('password');

				$user = SysAdminsTable::getInstance()->find($id);
				$user->password = hash('sha512', $password);
				$user->save();

				return '1';
			}
		}
	}
?>