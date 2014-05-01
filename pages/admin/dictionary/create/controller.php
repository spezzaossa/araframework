<?php
	class CreateController extends BackController {

		public function execute() {

			if(Request::isAJAX()) {

				$name = $this->request->getParam('name');
				$value = $this->request->getParam('value');
				$lang = $this->request->getParam('lang');

				try {
					$entry = new SysDictionary();
					$entry->name = $name;
					$entry->value = $value;
					$entry->id_language = $lang;
					$entry->save();

					return '1';
				} catch(Exception $e) {}

				return '-1';
			}

		}
	}
?>