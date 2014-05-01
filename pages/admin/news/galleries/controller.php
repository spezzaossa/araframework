<?php
	class GalleriesController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('name', 'num_photo', 'id_language', 'operazioni');

				$sQuery = SiteGalleriesTable::getInstance()->createQuery('a')
						->leftJoin('a.Images')
						->leftJoin('a.Language l')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.name LIKE '%".$_GET['sSearch']."%'");
				}
				$iFilteredTotal = $sQuery->count();

//				$sQuery->leftJoin('SiteGalleriesImages i ON a.id = i.gallery_id');
//				$sQuery->groupBy('a.id');
//				$sQuery->select('a.*, l.*, COUNT(*) AS num_photo');
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
						$row[] = $aRow['name'];
						$row[] = isset($aRow['Images']) ? count($aRow['Images']) : '0';
						$row[] = $aRow['Language']['language'];
						$row[] = '<span class="glyphicon glyphicon-link select" title="Collega" data-name="'.$aRow['name'].'" data-id="'.$aRow['id'].'"></span>';

						$output['aaData'][] = $row;
					}

				return $output;
			}

			// Parte comune a tutti
			$languages = SysLanguagesTable::getInstance()->createQuery()->fetchArray();

			$this->smarty->assign('languages', $languages);
		}
	}
?>