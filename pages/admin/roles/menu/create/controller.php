<?php
	class CreateController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				$label = $this->request->getParam('label');
				$link = $this->request->getParam('link');
				if ($label)
				{
					$menu = new SysAdminMenus();
					$menu->label = $label;
					$menu->link = $link;
					$menu->save();
				}

				return '1';
			}
		}
	}
?>