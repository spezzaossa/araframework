<?php

/**
 * SiteNews
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class SiteNews extends BaseSiteNews
{
	public function setUp() {
		$this->hasMany('SiteFiles as Files', array(
			'local' => 'id_news',
			'foreign' => 'id_file',
			'refClass' => 'SiteNewsFiles'));

		$this->hasOne('SysLanguages as Language', array(
			'local' => 'id_language',
			'foreign' => 'id'));

		$this->hasOne('SysSeo as Seo', array(
			'local' => 'id_seo',
			'foreign' => 'id'));

		$this->hasOne('SysSlugs as Slug', array(
			'local' => 'id_slug',
			'foreign' => 'id'));

		$this->hasOne('SiteGalleries as Gallery', array(
			'local' => 'id_gallery',
			'foreign' => 'id'));
	}

	public function getTranslations($lang = null) {
		$translations = SiteNewsTable::getInstance()->createQuery('a')
			->where('id_translation = ?', $this->id_translation);

		if ($lang) {
			$translations->leftJoin('a.Language l');
			$translations->andWhere("l.code = '?'", $lang);
		}

		return $translations->execute();
	}

	public function getURL() {
		return $this->id_slug ? $this->Slug->slug : 'news-'.$this->id;
	}

	public function getImage() {
		foreach($this->Files as $file)
			if ($file->type == 'Immagine')
				return $file;

		return null;
	}
}