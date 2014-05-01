<?php
	class LogoutController extends BackController {
		public function execute() {
			$this->session->logoutAdmin();
			header("Location: admin");
			exit();
		}
	}
?>