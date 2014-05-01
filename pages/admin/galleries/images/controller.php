<?php
	class ImagesController extends BackController {

		public function execute() {
			$id = $this->request->getParam('param_1');
			$gallery = SiteGalleriesTable::getInstance()->find($id);
			$page = 1;
			$per_page = 36;

			$query = SiteFilesTable::getInstance()->createQuery()
					->where("type LIKE 'Immagine'")
					->orderBy("created DESC")
			;
			$pager = new Doctrine_Pager($query, 1, $per_page);

			if (Request::isAJAX())
			{
				$page = $this->request->getParam('page_number');
				$pager->setPage($page);
			}

			$images = $pager->execute();
			$range = $pager->getRange(
				'Sliding',
				array(
					'chunk' => 5
				)
			);

			$this->smarty->assign('page_number', $page);
			$this->smarty->assign('range', $range->rangeAroundPage());
			$this->smarty->assign('images', $images);
			$this->smarty->assign('last', $pager->getLastPage());

			if (Request::isAJAX())
				return $this->smarty->fetch(dirname(__FILE__)."/gallery.html");

			$this->smarty->assign('gallery', $gallery);
		}
	}
?>