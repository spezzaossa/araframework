<?php
	class RolesController extends BackController {

		public function execute() {
			if(Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('id', 'n_utenti', 'name', 'operazioni');

				$sQuery = SysAdminRolesTable::getInstance()->createQuery('a')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.name LIKE '%".$_GET['sSearch']."%'");
				}
				$iFilteredTotal = $sQuery->count();

				$sQuery->offset($_GET['iDisplayStart']);
				$sQuery->limit($_GET['iDisplayLength']);
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
						$row = array();
						$row[] = $aRow->id;
						$row[] = $aRow->Admins->Count();
						$row[] = $aRow->name;
						$row[] = '
						<div id="row_'.$aRow->id.'">
							<a type="button" class="btn btn-default menu" href="admin-roles-menu-'.$aRow->id.'"><span class="glyphicon glyphicon-align-justify"></span> Menù</a>
							<button type="button" class="btn btn-default delete"><span class="glyphicon glyphicon-trash"></span> Elimina</button>
						</div>
						';

						$output['aaData'][] = $row;
					}

				return $output;
			}
		}
	}
?>