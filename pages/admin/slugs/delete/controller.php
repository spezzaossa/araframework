<?php
	class DeleteController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$slug = SysSlugsTable::getInstance()->find($id);
			if ($slug && $slug->automated == 0)
			{
				$slug->delete();
				return '1';
			}

			return '-1';
		}
	}
?>