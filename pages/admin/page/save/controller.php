<?php
	class SaveController extends BackController {

		public function execute() {

			if(Request::isAJAX()) {

				$id = $this->request->getParam('id');
				$value = $this->request->getParam('value');
				$title = $this->request->getParam('title');

				$entry = SiteContentsTable::getInstance()->find($id);
				$entry->title = $title;
				$entry->content = $value;
				$entry->save();

				return 1;
			}

		}
	}
?>