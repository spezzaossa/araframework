<?php
	class SaveController extends BackController {

		public function execute() {

			if(Request::isAJAX()) {

				$id = $this->request->getParam('id');
				$value = $this->request->getParam('value');

				$entry = SysDictionaryTable::getInstance()->find($id);
				$entry->value = $value;
				$entry->save();

				return '1';
			}

		}
	}
?>