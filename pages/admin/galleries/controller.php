<?php
	class GalleriesController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('name', 'num_photo', 'id_language', 'operazioni');

				$sQuery = SiteGalleriesTable::getInstance()->createQuery('a')
						->leftJoin('a.GalleryImage')
						->leftJoin('a.Language l')
						->leftJoin('a.Page p')
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
						$row[] = isset($aRow['GalleryImage']) ? count($aRow['GalleryImage']) : '0';
						$row[] = $aRow['Language']['language'];
						$row[] = ucwords($aRow['Page']['name']);
						$row[] = $aRow['tag'];
						$row[] = '
						<div id="row_'.$aRow['id'].'">
							<a class="glyphicon glyphicon-edit" title="Modifica" href="admin-galleries-edit-'.$aRow['id'].'"></a>
							<a class="glyphicon glyphicon-picture" title="Immagini" href="admin-galleries-images-'.$aRow['id'].'"></a>
							<a class="glyphicon glyphicon-file clone" title="Duplica" href="admin-galleries-clone-'.$aRow['id'].'"></a>
							<span class="glyphicon glyphicon-trash delete" title="Elimina file"></span>
						</div>
						';

						$output['aaData'][] = $row;
					}

				return $output;
			}

			$this->smarty->assign('languages', SysLanguagesTable::getInstance()->createQuery()->fetchArray());
			$this->smarty->assign('pages', SysPagesTable::getInstance()->createQuery()->execute());
		}
	}
?>