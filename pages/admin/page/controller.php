	<?php
		class PageController extends BackController {

			public function execute() {
				$page = $this->request->getParam('p');
				$this->smarty->assign('p', ucwords(SysAliases::getLocalizedPageName($page, DEFAULT_LANG)));
				// Parte comune a tutti
				$languages = SysLanguagesTable::getInstance()->createQuery('a')
						->fetchArray();

				$this->smarty->assign('languages', $languages);

				$contents_per_language = array();

				foreach($languages as $language)
				{
					$contents = SiteContentsTable::getInstance()->createQuery('a')
							->leftJoin('a.Page')
							->where('a.Page.name = ?', $page)
							->andWhere('a.Page.editable = 1')
							->andWhere('a.id_language = ?', $language['id'])
							->orderBy('a.tag, a.sort_order ASC')
							->fetchArray();

					$contents_per_language[$language['id']] = $contents;
				}

				$this->smarty->assign('contents', $contents_per_language);

				// Controllo se la pagina ha una tabella apposita
//				$mysql_connection = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS);
//				mysql_select_db(MYSQL_NAME, $mysql_connection);
//				$query = "SHOW TABLES LIKE '".MYSQL_CONTENT_PREFIX."_".$page."'";
//				$result = mysql_query($query);
//
//				if (mysql_num_rows($result) > 0)
//				{
//
//				}
			}
		}
	?>