<?php

/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */
/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 * */
require_once dirname(__FILE__) . '/../../config.php';
require_once dirname(__FILE__) . '/../../core/utils.php';

global $js_files;
foreach ($js_files as $js_file)
	$tmp_js[] = '//resource/js/' . $js_file;

global $css_files;
foreach ($css_files as $css_file)
	$tmp_css[] = '//resource/css/' . $css_file;

global $js_admin_files;
foreach ($js_admin_files as $js_admin_file)
	$tmp_admin_js[] = '//resource/js/' . $js_admin_file;

global $css_admin_files;
foreach ($css_admin_files as $css_admin_file)
	$tmp_admin_css[] = '//resource/css/' . $css_admin_file;

$array_file_css_js = array(
	'js' => $tmp_js,
	'css' => $tmp_css,
	'admin_js' => $tmp_admin_js,
	'admin_css' => $tmp_admin_css
);

//controlla se esistono le cartelle del page tree all'interno della cartella css e quella js. Se, al suo interno è presente qualche file .css o .js, allora assegna le variabili che verranno richiamate nel main per la minificazione
$page = str_replace('-', '/', $_GET['g']);
$array_files = array();
$array_files_path = array();
$page_tree_dir = '';
if (substr($page, -4) == '_css') {
	$file_extension = 'css';
	$page = substr($page, 0, -4);
	$page_tree_dir = dirname(__FILE__) . "/../../resource/css/" . $page . "/";
} elseif(substr($page, -3) == '_js'){
	$file_extension = 'js';
	$page = substr($page, 0, -3);
	$page_tree_dir = dirname(__FILE__) . "/../../resource/js/" . $page . "/";
}

if (is_dir($page_tree_dir)) {
	$array_files = Utils::getFilesFromDir($page_tree_dir, $file_extension);

	foreach($array_files as $item_array){
		$array_file_css_js[$_GET['g']][] = $page_tree_dir . $item_array;
	}
}

return $array_file_css_js;