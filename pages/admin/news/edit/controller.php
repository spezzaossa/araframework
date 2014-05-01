<?php
	class EditController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$news = SiteNewsTable::getInstance()->find($id);

			if (Request::isPOST()) {
				$redirect = true;
				$params = $this->request->getParams();

				if (!isset($params['gallery'])) $params['gallery'] = 0;
				if (!isset($params['content'])) $params['content'] = '';
				if (!isset($params['attachments'])) $params['attachments'] = '';

				if (!$news) {
					$redirect = false;
					$news = new SiteNews();
					$news->id_language =  $params['lang']; // Solo alla creazione
				}
				$date = DateTime::createFromFormat('d/m/Y', $params['date']);
				$news->date = $date->format('Y-m-d');
				$news->title = $params['title'];
				$news->content = $params['content'];
				$news->id_gallery = $params['gallery'];
				$news->active = $params['active'];
				$news->save();

				if (isset($params['seo_title']) || isset($params['seo_descr']) || isset($params['seo_keywords']))
				{
					if (!$news->id_seo)
					{
						$seo = new SysSeo();
						$seo->meta_title = $news->title;
						$seo->save();
						$news->id_seo = $seo->id;
						$news->save();
					}

					$news->Seo->meta_title = $params['seo_title'];
					$news->Seo->meta_description = $params['seo_descr'];
					$news->Seo->meta_keywords = $params['seo_keywords'];
					$news->Seo->save();
				}

				if (isset($params['slug']))
				{
					if ($params['slug'] == '')
					{
						if ($news->id_slug)
						{
							$news->Slug->delete();
							$news->id_slug = 0;
							$news->save();
						}
					}
					else
					{
						if (!$news->id_slug)
						{
							$slug = new SysSlugs();
							$slug->automated = 1;
							$slug->page_url = 'news-'.$news->id;
							$slug->save();
							$news->id_slug = $slug->id;
							$news->save();
						}

						$news->Slug->slug = $params['slug'];
						$news->Slug->save();
					}
				}

				SiteNewsFilesTable::getInstance()->createQuery('a')
					->delete()
					->where('id_news = ?', $news->id)
					->execute()
				;

				$attachments = explode(',', $params['attachments']);
				$count = 1;
				foreach ($attachments as $attachment)
				{
					$file = SiteFilesTable::getInstance()->find(intval($attachment));
					if ($file)
					{
						$news_file = new SiteNewsFiles();
						$news_file->id_news = $news->id;
						$news_file->id_file = $file->id;
						$news_file->sort_order = $count++;
						$news_file->save();
					}
				}

				if ($redirect)
					header('Location: admin-news');
				else
					header('Location: admin-news-edit-'.$news->id);
				exit;
			}

			$this->smarty->assign('attachments', $news->Files);
			$this->smarty->assign('news', $news);
		}
	}
?>