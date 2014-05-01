<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.translate.php
 * Type:     function
 * Name:     translate
 * Purpose:  traduce
 * -------------------------------------------------------------
 */
require_once dirname(__FILE__).'/../../../core/utils.php';
function smarty_function_translate($params, Smarty_Internal_Template $template)
{
	$word = $params['word'];
	$translated_word = Utils::translate($word);
	if($translated_word) $word = $translated_word;
	else $word = '#MISSING#'.$word;
    return $word;
}
?>