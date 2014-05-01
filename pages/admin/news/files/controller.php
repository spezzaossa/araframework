<?php
	class FilesController extends BackController {

		public function execute() {

			if(Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('preview', 'filename', 'type', 'created', 'operazioni');

				$sQuery = SiteFilesTable::getInstance()->createQuery('a')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.filename LIKE '%".$_GET['sSearch']."%' OR a.type LIKE '%".$_GET['sSearch']."%'");
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
						switch($aRow['type'])
						{
							case 'Immagine':
								$row[] = '<img src="resource/files/th_'.$aRow['filename'].'?t='.time().'" style="max-height: 24px; max-width: 40px">'; break;
							default:
								$row[] = ''; break;
						}
						$row[] = '<a href="resource/files/'.$aRow['filename'].'" target="_blank">'.$aRow['filename'].'</a>';
						$row[] = $aRow['type'];
						$row[] = date('d/m/Y H:i:s', strtotime($aRow['created']));
						$row[] = '
							<span class="glyphicon glyphicon-paperclip attach" title="Allega" data-filename="'.$aRow['filename'].'" data-id="'.$aRow['id'].'"></span>
						';

						$output['aaData'][] = $row;
					}

				return $output;
			}
		}
	}
?>