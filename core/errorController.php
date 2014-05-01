<?php
	abstract class ErrorController extends Controller {

		public function renderPage() {
			$GLOBALS['TEMPLATE'] =	'error';
			$this->smarty->assign('code', $this->page);
			parent::renderPage();
		}
	}
?>