<?php
	class DeleteController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$page = SysPagesTable::getInstance()->find($id);
			if ($page) {
				SysAliasesTable::getInstance()->createQuery()
					->delete()
					->where('id_page = ?', $page->id)
					->execute();

				SiteContentsTable::getInstance()->createQuery()
					->delete()
					->where('id_page = ?', $page->id)
					->execute();

				$page->delete();
			}

			return '1';
		}
	}
?>