<?php

/**
 * SysAdminRoles
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class SysAdminRoles extends BaseSysAdminRoles
{
	public function setUp() {
		$this->hasMany('SysAdmins as Admins', array(
				'local' => 'id',
				'foreign' => 'id_role'));
		$this->hasMany('SysRolesMenus as Menus', array(
				'orderBy' => 'sort_order',
				'local' => 'id',
				'foreign' => 'id_role'));
	}
}