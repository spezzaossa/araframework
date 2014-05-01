<?php
	class DeleteController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$meta = SiteFilesMetaTable::getInstance()->find($id);
			if ($meta)
			{
				$meta->delete();
				return '1';
			}

			return '-1';
		}
	}
?>