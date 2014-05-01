<?php
	abstract class BackController extends Controller {

		/*
		 * Modifica il template di default
		 */
		public function renderPage() {
			if ($this->session->isLogged()) $this->session->logout();
			if (!$this->canAccessMenu())
			{
				if (!SOFT_ERRORS)
				{
					header("Status: 404 Not Found");
					header("HTTP/1.0 404 Not Found");
					$error = new errorsController($this->request, $this->session, 404);
					$error->renderPage();
					exit;
				}
				else
				{
					header('Location: '._BASE_HREF.SOFT_ERRORS_REDIRECT);
					exit();
				}
			}

			$GLOBALS['TEMPLATE'] =	'admin';
			$menu = self::generateMenu();
			$this->smarty->assign('menu', $menu);	// Vuoto se l'utente non è loggato
			$this->smarty->assign('azienda', _AZIENDA);
			$this->smarty->assign('admin', 1);		// CSS per template
			if ($this->session->user && $this->session->user->id_role == -1)
			{
				$roles = SysAdminRolesTable::getInstance()->createQuery()->execute();
				$this->smarty->assign('roles', $roles);
				$this->smarty->assign('superadmin', 1);
			}
			parent::renderPage();
		}

		private function canAccessMenu() {
			switch($this->pagetree)
			{
				case 'admin':
				case 'admin-login':
				case 'admin-logout':
				case 'admin-home':
				case 'admin/login':
				case 'admin/logout':
				case 'admin/home':
					return true;
			}

			if (!$this->session->isAdminLogged()) return false;

			if ($this->session->user->id_role == -1)
				return true;
			else
			{
				$link = $this->pagetree;
				$link2 = str_replace('/', '-', $link);
				
				switch($link)
				{
					case 'admin-page':
					case 'admin/page':
					case 'admin-page-save':
					case 'admin/page/save':
					{
						$link = '{contents}';
						unset($link2);
					}
				}

				$query = SysRolesMenusTable::getInstance()->createQuery('a')
						->innerJoin('a.Menu m')
						->where('a.id_role = ' . $this->session->user->id_role);

				if (isset($link2))
					$query->andWhere("(LOCATE(link, '{$link}') > 0 OR LOCATE(link,'{$link2}')) > 0");
				else
					$query->andWhere("LOCATE(link, '{$link}') > 0");

				$menu = $query->fetchOne();

				if ($menu && $menu->id) return true;
			}

			return false;
		}

		public function generateMenu() {
			$menu = '';

			if ($this->session->admin)
			{
				$items = $this->session->user->Menus;
				foreach($items as $item)
				{
					$active = false;
					if (!$item->id_parent)
					{
						if (preg_match('#\{(\w+)\}#', $item->Menu->link, $match))
						{
							$menu .= $this->getMenuBlock($match[1]);
						}
						else
						{
							$children = $item->Children;
							if (stripos($this->url, $item->Menu->link) === 0) $active = true;
							if (count($children) > 0)
							{
								$childmenu = '';
								foreach($children as $child)
								{
									if (stripos($this->url, $child->Menu->link) === 0) $active = true;
									$childmenu .= '<li><a href="'.$child->Menu->link.'">'.$child->Menu->label.'</a></li>';
								}
								$menu .= '<li class="dropdown'.($active ? ' active' : '').'">';
								$menu .= '<a href="'.$item->Menu->link.'" class="dropdown-toggle" data-toggle="dropdown">'.$item->Menu->label.' <b class="caret"></b></a>';
								$menu .= '<ul class="dropdown-menu">'.$childmenu.'</ul>';
								$menu .= '</li>';
							}
							else
							{
								$menu .= '<li'.($active ? ' class="active"' : '').'><a href="'.$item->Menu->link.'">'.$item->Menu->label.'</a></li>';
							}
						}
					}
				}
			}

			return $menu;

		}

		public static function getMenuBlock($block_name)
		{
			$menu = '';

			switch($block_name)
			{
				case 'contents':
					$menu .= '<li class="dropdown">';
					$menu .= '<a href="#testi" class="dropdown-toggle" data-toggle="dropdown">Testi <b class="caret"></b></a>';
					$menu .= '<ul class="dropdown-menu">';
						//$menu .= '<li><a href="#">Nuova pagina</a></li>';
						$pagine  = SysPagesTable::getInstance()->createquery('p')
								->where('p.editable = 1')
								->fetchArray();
						foreach ($pagine as $pagina) {
							$menu .= '<li><a href="admin-page?p='.$pagina['name'].'">'.ucwords(SysAliases::getLocalizedPageName($pagina['name'], DEFAULT_LANG)).'</a></li>';
						}

					$menu .= '</ul>';
					$menu .= '</li>';
					break;
			}

			return $menu;
		}
	}
?>