<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.getFilesFromDir.php
 * Type:     function
 * Name:     getFilesFromDir
 * Purpose:  legge il file presenti in una cartella. Si può scegliere estensione file e numero di file da ritornare.
 * -------------------------------------------------------------
 */
require_once dirname(__FILE__).'/../../../core/utils.php';
function smarty_function_getFilesFromDir($params, Smarty_Internal_Template $template)
{
	$directory = $params['directory'];
	$extensions = $params['extensions'];
	$nToShow = $params['nToShow'];
	$shuffleArray = $params['shuffleArray'];
	$arrayFiles = Utils::getFilesFromDir($directory, $extensions, $nToShow, $shuffleArray);

	$arrayFileToShow = '';
	if($arrayFiles) {
		$arrayFileToShow = array();
		$arrayFileToShow = $arrayFiles;
	}
	else $arrayFileToShow = '#MISSING#' . $arrayFileToShow;
    return $arrayFileToShow;
}
?>