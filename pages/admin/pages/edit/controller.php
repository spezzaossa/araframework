<?php
	class EditController extends BackController
	{
		public function execute()
		{
			$id = $this->request->getParam('param_1');
			$page = SysPagesTable::getInstance()->find($id);

			if (Request::isPOST())
			{
				$redirect = true;
				$params = $this->request->getParams();

				if (!isset($params['editable'])) $params['editable'] = 0;

				if (!$page)
				{
					$redirect = false;
					$page = new SysPages();
					$name = str_replace(array(' ','-'), '_', $params['name']);
					$page->name = $name;
					$page->id_top = $params['top'];
					$path = dirname(__FILE__).'/../../../'.$page->Tree;

					if (!file_exists($path) || !is_dir($path))
						mkdir($path);

					if (!file_exists($path.'/view.html'))
						fclose(fopen($path.'/view.html', 'w'));

					if (!file_exists($path.'/controller.php'))
					{
						$this->smarty->assign('pagename', ucfirst($name));
						file_put_contents($path.'/controller.php', $this->smarty->fetch(dirname(__FILE__)."/controller.html"));
					}
				}
				$page->editable = $params['editable'];
				$page->save();

				foreach($params['aliases'] as $key => $value)
				{
					$alias = SysAliasesTable::getInstance()->createQuery()
						->where('id_page = ?', $page->id)
						->andWhere('id_language = ?', $key)
						->fetchOne();

					if (!$alias && $value)
					{
						$alias = new SysAliases();
						$alias->id_page = $page->id;
						$alias->id_language = $key;
					}
					if ($alias)
					{
						if ($value)
						{
							$alias->value = $value;
							$alias->save();
						}
						else
						{
							$alias->delete();
						}
					}
				}

				foreach($params['contents'] as $key => $value)
				{
					$content = SiteContentsTable::getInstance()->find($key);
					if ($content)
					{
						$content->tag = $value['tag'];
						$content->title = $value['title'];
						$content->content = $value['content'];
					}
				}

				if (isset($params['action']) && $params['action'] != 'save') // Sto creando un nuovo content
				{
					$redirect = false;
					$last_content = SiteContentsTable::getInstance()->createQuery()
						->where('id_page = ?', $page->id)
						->andWhere('id_language = ?', intval($params['action']))
						->orderBy('sort_order DESC')
						->fetchOne();

					$content = new SiteContents();
					$content->id_page = $page->id;
					$content->id_language = intval($params['action']);
					$content->sort_order = ($last_content) ? $last_content->sort_order + 1 : 0;
					$content->save();
				}

				if ($redirect)
					header('Location: admin-pages');
				else
					header('Location: admin-pages-edit-'.$page->id);
				exit;
			}

			$contents_per_language = array();
			$aliases_per_language = array();
			$languages = SysLanguagesTable::getInstance()->createQuery()->fetchArray();

			foreach($languages as $language)
			{
				$contents = SiteContentsTable::getInstance()->createQuery('a')
						->where('a.id_page = ?', $page->id)
						->andWhere('a.id_language = ?', $language['id'])
						->orderBy('a.sort_order ASC')
						->fetchArray();

				$alias = SysAliasesTable::getInstance()->createQuery('a')
						->where('a.id_page = ?', $page->id)
						->andWhere('a.id_language = ?', $language['id'])
						->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

				$contents_per_language[$language['id']] = $contents;
				$aliases_per_language[$language['id']] = $alias;
			}

			$this->smarty->assign('contents', $contents_per_language);
			$this->smarty->assign('aliases', $aliases_per_language);

			$this->smarty->assign('languages', $languages);
			$this->smarty->assign('the_page', $page);
		}
	}
?>