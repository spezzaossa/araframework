<?php
	class DeleteController extends BackController {

		public function execute() {
			$id = $this->request->getParam('id');

			$news = SiteNewsTable::getInstance()->find($id);
			if ($news) {
				SiteNewsFilesTable::getInstance()->createQuery()
					->delete()
					->where('id_news = ?', $news->id)
					->execute();

				SysTranslationsTable::getInstance()->createQuery()
					->delete()
					->where('id_translation = ?', $news->id_translation)
					->andWhere('id_object = ?', $news->id)
					->execute();

				$news->Seo->delete();
				$news->delete();
			}

			return '1';
		}
	}
?>