<?php
	class NewsController extends BackController {

		public function execute() {

			if (Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('date', 'title', 'language', 'operazioni');

				$sQuery = SiteNewsTable::getInstance()->createQuery('a')
						->leftJoin('a.Language')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.title LIKE '%".$_GET['sSearch']."%' OR a.content LIKE '%".$_GET['sSearch']."%'");
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
						$row[] = date('d/m/Y', strtotime($aRow['date']));

						if ($aRow['active'])
							$title = '<span class="label label-success">Pubblicata</span> ';
						else
							$title = '<span class="label label-warning">Bozza</span> ';
						$title .= $aRow['title'];

						$row[] = $title;
						$row[] = $aRow['Language']['language'];
						$row[] = '
							<a class="glyphicon glyphicon-edit" title="Modifica" href="admin-news-edit-'.$aRow['id'].'"></a>
							<a class="glyphicon glyphicon-book" title="Traduzioni" href="admin-news-translate-'.$aRow['id'].'"></a>
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