<?php
	class UsersController extends BackController {

		public function execute() {
			if(Request::isAJAX())
			{
				/* colonne da visualizzare nella tabella */
				$aColumns = array('id', 'username', 'ruolo', 'operazioni');

				$sQuery = SysAdminsTable::getInstance()->createQuery('a')
//						->andWhere('id_role > 0')
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
						$row[] = $aRow->username;
						$row[] = ($aRow->id_role == -1) ? '*AMMINISTRATORE*' : $aRow->Role->name;
						$row[] = '
						<div id="row_'.$aRow->id.'">
							<button type="button" class="btn btn-default psw"><span class="glyphicon glyphicon-asterisk"></span> Cambia Password</button>
							'.(($aRow->id_role == -1) ? '' : '<button type="button" class="btn btn-default delete"><span class="glyphicon glyphicon-trash"></span> Elimina</button>').'
						</div>
						';

						$output['aaData'][] = $row;
					}

				return $output;
			}


			$roles = SysAdminRolesTable::getInstance()->createQuery('a')
					->orderBy('name ASC')
					->execute();

			$this->smarty->assign('roles', $roles);
		}
	}
?>