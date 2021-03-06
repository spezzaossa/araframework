<?php

/**
 * SiteNewsFiles
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class SiteNewsFiles extends BaseSiteNewsFiles
{
	public function setUp() {
		$this->hasOne('SiteFiles as File', array(
				'local' => 'id_file',
				'foreign' => 'id'));
		$this->hasOne('SiteNews as News', array(
				'local' => 'id_news',
				'foreign' => 'id'));
	}
}