<?php
	class UpdateController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');
			$name = $this->request->getParam('name');
			$gallery = SiteFilesTable::getInstance()->find($id);
			
			if ($gallery)
			{
				$gallery->name = $name;
				$gallery->save();
			}

			return '1';
		}
	}
?>