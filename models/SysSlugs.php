<?php

/**
 * SysSlugs
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */

class SysSlugs extends BaseSysSlugs
{
	public static function getLocalizedSlug($page, $lang = null, $class = null, $slug = null)
	{
		$lang = (!$lang) ? $_SESSION['lang'] : $lang;

		if ($class && $slug)
			return call_user_func_array(array($class, 'getLocalizedSlug'), array($slug, $lang));

		$page_alias = SysAliasesTable::getInstance()->createQuery('a')
				->leftJoin('a.Page')
				->leftJoin('a.Language')
				->where('a.Page.name = ?', $page)
				->andWhere('a.Language.code = ?', $lang)
				->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

		return $page_alias['value'];
	}
}