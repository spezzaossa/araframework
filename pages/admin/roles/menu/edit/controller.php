<?php
	class EditController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				$id			= intval($this->request->getParam('param_1'));
				$elements	= explode(',', $this->request->getParam('elements'));
				$c = 0;
				foreach($elements as $element)
				{
					$elem = SysRolesMenusTable::getInstance()->find($element);
					$elem->sort_order = $c;
					$elem->save();
					$c++;
				}

				return '1';
			}
		}
	}
?>