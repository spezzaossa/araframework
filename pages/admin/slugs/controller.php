<?php
	class SlugsController extends BackController {

		public function execute() {

			if (Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('slug', 'page_url', 'operazioni');

				$sQuery = SysSlugsTable::getInstance()->createQuery('a')
						->where('automated = 0')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.slug LIKE '%".$_GET['sSearch']."%' OR a.page_url LIKE '%".$_GET['sSearch']."%'");
				}
				$iFilteredTotal = $sQuery->count();

				$sQuery->offset($_GET['iDisplayStart']);
				$sQuery->limit($_GET['iDisplayLength']);
				$rResult = $sQuery->fetchArray();

				/* Output */
				$output = array(
					"sEcho" => intval($_GET['sEcho']),
					"iTotalRecords" => $iTotal,
					"iTotalDisplayRecords" => $iFilteredTotal,
					"aaData" => array()
				);

				if (isset($rResult))
					foreach ($rResult as $aRow) {
						$row = array();
//						$row['DT_RowId'] = $aRow['id'];
						$row[] = $aRow['slug'];
						$row[] = $aRow['page_url'];
						$row[] = '
							<span class="glyphicon glyphicon-edit edit" title="Modifica" data-id="'.$aRow['id'].'" data-slug="'.$aRow['slug'].'" data-url="'.$aRow['page_url'].'"></span>
							<span class="glyphicon glyphicon-trash delete" title="Elimina" data-id="'.$aRow['id'].'"></span>
						';

						$output['aaData'][] = $row;
					}

				return $output;
			}

			$languages = SysLanguagesTable::getInstance()->createQuery()->fetchArray();
			$this->smarty->assign('languages', $languages);
		}
	}
?>