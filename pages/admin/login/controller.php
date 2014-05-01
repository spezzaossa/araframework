<?php
	class LoginController extends BackController {
		
		public function execute() {
			$username = $this->request->getParam('username');
			$password = $this->request->getParam('password');
			$esito = $this->session->loginAdmin($username, hash('sha512',$password));
			if($esito) {
				header("Location: admin-home");
			} else {
				header("Location: admin-home?err=login");
			}
			exit();
		}
	}
?>