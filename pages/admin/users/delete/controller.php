<?php
	class DeleteController extends BackController {

		public function execute() {
			if(Request::isAJAX())
			{
				$id	  = intval($this->request->getParam('param_1'));
				$user = SysAdminsTable::getInstance()->find($id);
				if ($user->id_role > 0) $user->delete();

				return '1';
			}
		}
	}
?>