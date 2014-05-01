<?php
	ini_set('display_errors', 1);
	require_once(dirname(__FILE__) . '/../config.php');
	require_once(dirname(__FILE__) . '/../lib/doctrine/Doctrine.php');

	spl_autoload_register(array('Doctrine', 'autoload'));
	Doctrine_Manager::connection('mysql://'.MYSQL_USER.':'.MYSQL_PASS.'@'.MYSQL_HOST.'/'.MYSQL_NAME, 'connection');
	Doctrine_Core::generateModelsFromDb('models', array('connection'), array('generateTableClasses' => true));
	
	if(!is_dir(dirname(__FILE__).'/../models')) mkdir(dirname(__FILE__).'/../models');
	if(!is_dir(dirname(__FILE__).'/../models/generated')) mkdir(dirname(__FILE__).'/../models/generated');
	
	$folder = opendir(dirname(__FILE__).'/models/generated');
	while(($file = readdir($folder))!==false) {
		if($file != '.' && $file!='..') {
			copy(dirname(__FILE__).'/models/generated/'.$file, dirname(__FILE__).'/../models/generated/'.$file);
			unlink(dirname(__FILE__).'/models/generated/'.$file);
		}
	}
	rmdir(dirname(__FILE__).'/models/generated');
	
	$folder = opendir(dirname(__FILE__).'/models');
	while((($file = readdir($folder))!==false)) {
		if($file != '.' && $file!='..') {
			if(!file_exists(dirname(__FILE__).'/../models/'.$file)) {
				copy(dirname(__FILE__).'/models/'.$file, dirname(__FILE__).'/../models/'.$file);
			}
			unlink(dirname(__FILE__).'/models/'.$file);
		}
	}
	
	rmdir(dirname(__FILE__).'/models');
?>
Generazione modello completata!
