<?php
	class MetaController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');

			if(Request::isAJAX()) {
				/* colonne da visualizzare nella tabella */
				$aColumns = array('title', 'alt', 'id_language', 'operazioni');

				$sQuery = SiteFilesMetaTable::getInstance()->createQuery('a')
						->leftJoin('a.Language')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
						->where('id_file = ?', $id)
				;

				$iTotal = $sQuery->count();

				if (isset($_GET['sSearch']) && trim($_GET['sSearch']) != '') {
					$sQuery->andWhere("a.name LIKE '%".$_GET['sSearch']."%' OR a.value LIKE '%".$_GET['sSearch']."%'");
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
						$row[] = $aRow['title'];
						$row[] = $aRow['alt'];
						$row[] = $aRow['Language']['language'];
						$row[] = '
						<div id="row_'.$aRow['id'].'">
							<span class="glyphicon glyphicon-trash delete" title="Elimina attributi"></span>
							<img class="loading" src="resource/img/loading.gif" />
						</div>
						';

						$output['aaData'][] = $row;
					}

				return $output;
			}

			// Parte comune a tutti
			$languages = SysLanguagesTable::getInstance()->createQuery()->fetchArray();
			$file = SiteFilesTable::getInstance()->find($id);

			$this->smarty->assign('languages', $languages);
			$this->smarty->assign('file', $file);
		}
	}
?>