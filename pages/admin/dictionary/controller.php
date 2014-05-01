<?php
	class DictionaryController extends BackController {

		public function execute() {

			if(Request::isAJAX()) {

				$language_id = $this->request->getParam('param_1');

				/* colonne da visualizzare nella tabella */
				$aColumns = array('id', 'name', 'value', 'operazioni');

				$sQuery = SysDictionaryTable::getInstance()->createQuery('a')
						->leftJoin('a.Language')
						->orderBy($aColumns[$_GET['iSortCol_0']].' '.$_GET['sSortDir_0'])
						->where('id_language = ?', $language_id)
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
						$row[] = $aRow['id'];
//						$row[] = $aRow['Language']['language'];
						$row[] = $aRow['name'];
						$row[] = '<input class="form-control" id="input_'.$aRow['id'].'" type="text" value="'.$aRow['value'].'" original="'.$aRow['value'].'" />';
						$row[] = '
						<div id="row_'.$aRow['id'].'">
							<span class="glyphicon glyphicon-floppy-disk save" title="Salva modifiche"></span>
							<span class="glyphicon glyphicon-remove cancel" title="Annulla modifiche"></span>
							<img class="loading" src="resource/img/loading.gif" />
						</div>
						';

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