<?php
	class Request {
		private $page 		= null;
		private $params		= array();

		public function __construct() {
			$this->page = HOME_PAGE;
			if(isset($_REQUEST['page'])) {
				$page = $_REQUEST['page'];
				if(!empty($page)) {
					$this->page = $page;
				} else {
					$_REQUEST['page'] = HOME_PAGE;
					$this->page = HOME_PAGE;
				}
			}
			foreach($_REQUEST as $key=>$value) {
				$this->params[$key] = $value;
				unset($_REQUEST[$key]);
			}
		}

		public function getPage() {
			return $this->page;
		}

		public function getParam($param) {
			if(!isset($this->params[$param])) return null;
			return $this->params[$param];
		}

		public function getParams() {
			return $this->params;
		}

		public function setParam($key, $value) {
			$this->params[$key] = $value;
		}

		public function setPage($page) {
			$this->page = $page;
		}

		public function getQueryString() {
			$result = '';
			foreach($this->params as $param => $value)
			{
				if (substr($param, 0, 6) == 'param_')
					$result .= '-'.$value;
			}
			return $result;
		}

		public static function isAJAX() {
			return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		}

		public static function isGET() {
			return $_SERVER['REQUEST_METHOD'] == 'GET';
		}

		public static function isPOST() {
			return $_SERVER['REQUEST_METHOD'] == 'POST';
		}
	}
?>
