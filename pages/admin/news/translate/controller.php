<?php
	class TranslateController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$lang = $this->request->getParam('param_2');
			$news = SiteNewsTable::getInstance()->find($id);
			$languages = SysLanguagesTable::getInstance()->createQuery()->execute();

			if ($lang)
			{
				$translation = new SiteNews();

				if (!$news->id_translation) {
					$lastid = SysTranslationsTable::getInstance()->createQuery()
						->select('MAX(id_translation)+1 as value')
						->fetchOne()
					;

					$t = new SysTranslations();
					$t->id_translation = $lastid['value'] ? $lastid['value'] : 1;
					$t->id_object = $news->id;
					$t->save();

					$news->id_translation = $t->id_translation;
					$news->save();
				}

				$translation->id_language = $lang;
				$translation->id_translation = $news->id_translation;
				$translation->id_gallery = $news->id_gallery;
				$translation->date = $news->date;
				$translation->title = $news->title;
				$translation->content = $news->content;
				$translation->save();

				$t = new SysTranslations();
				$t->id_translation = $news->id_translation;
				$t->id_object = $translation->id;
				$t->save();

				$attachments = SiteNewsFilesTable::getInstance()->createQuery('a')
					->where('id_news = ?', $id)
					->execute()
				;

				foreach($attachments as $attachment)
				{
					$a = new SiteNewsFiles();
					$a->id_news = $translation->id;
					$a->id_file = $attachment->id_file;
					$a->save();
				}

				header('Location: admin-news-edit-'.$translation->id);
				exit;
			}

			$translationTable = array();

			foreach($languages as $language)
			{
				$translationTable[$language->id] = array(
					'title' => false,
					'Language' => $language
				);
			}

			if ($news->id_translation)
			{
				foreach($news->getTranslations() as $translation)
				{
//					$t_news = SiteNewsTable::getInstance()->find($translation->object_id);
//					$translationTable[$t_news->id_language] = $t_news;
					$translationTable[$translation->id_language] = $translation;
				}
			}
			else
				$translationTable[$news->id_language] = $news;

			$this->smarty->assign('translations', $translationTable);
			$this->smarty->assign('languages', $languages);
			$this->smarty->assign('news', $news);
		}
	}
?>