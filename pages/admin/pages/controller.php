<?php
	class PagesController extends BackController {

		private function printPages(&$output, $aRow, $level)
		{
			$row = array();
			if ($level > 0)
			{
				$name = '';
				if ($level) $name = '<span style="padding-left:'.($level*25).'px"></span>';
				$row[] = $name.'&RightTee;&HorizontalLine; '.$aRow->data['name'];
			}
			else
				$row[] = $aRow->data['name'];

			$row[] = count($aRow->Aliases);
			$row[] = count($aRow->Contents);
			$row[] = '
				<a class="glyphicon glyphicon-edit" title="Modifica" href="admin-pages-edit-'.$aRow->id.'"></a>
				<span class="glyphicon glyphicon-trash delete" title="Elimina" data-id="'.$aRow->id.'"></span>
			';

			$output['aaData'][] = $row;

			foreach($aRow->Children as $child)
			{
				$this->printPages($output, $child, $level + 1);
			}
		}

		public function execute() {

			if (Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('name', 'contents', 'aliases', 'operazioni');

				$sQuery = SysPagesTable::getInstance()->createQuery('a')
						->leftJoin('a.Aliases')
						->leftJoin('a.Contents')
						->where('id_top = 0')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.name LIKE '%".$_GET['sSearch']."%'");
				}
				$iFilteredTotal = $sQuery->count();

				$rResult = $sQuery->execute();

				/* Output */
				$output = array(
					"sEcho" => intval($_GET['sEcho']),
					"iTotalRecords" => $iTotal,
					"iTotalDisplayRecords" => $iFilteredTotal,
					"aaData" => array()
				);

				if (isset($rResult))
					foreach ($rResult as $aRow) {
						$this->printPages($output, $aRow, 0);
					}

				return $output;
			}

			$languages = SysLanguagesTable::getInstance()->findAll(Doctrine::HYDRATE_ARRAY);
			$pages = SysPagesTable::getInstance()->findBy('id_top', 0);

			$this->smarty->assign('languages', $languages);
			$this->smarty->assign('pages', $pages);
			$this->smarty->assign('tpl_options', dirname(__FILE__).'/options.html');
		}
	}
?>