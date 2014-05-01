<?php
	//Importo il file di configurazione
	require_once dirname(__FILE__).'/config.php';

	//Importo il 'core' del framework
	require_once dirname(__FILE__).'/core/session.php';
	require_once dirname(__FILE__).'/core/request.php';
	require_once dirname(__FILE__).'/core/controller.php';
	require_once dirname(__FILE__).'/core/backController.php';
	require_once dirname(__FILE__).'/core/errorController.php';
	require_once dirname(__FILE__).'/core/utils.php';
	require_once dirname(__FILE__).'/core/widget.php';

	require_once dirname(__FILE__).'/pages/errors/controller.php';

	//Importo le librerie di terze parti per la gestione del model e del view
	require_once dirname(__FILE__).'/lib/doctrine/Doctrine.php';
	require_once dirname(__FILE__).'/lib/smarty/Smarty.class.php';

	//Imposto l'autoloading delle classi del modello
	spl_autoload_register(array('Doctrine', 'autoload'));
	Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
	Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
	Doctrine_Manager::getInstance()->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
	Doctrine_Manager::connection('mysql://'.MYSQL_USER.(strlen(MYSQL_PASS)? ':'.MYSQL_PASS : '').'@'.MYSQL_HOST.'/'.MYSQL_NAME, 'connection');
	Doctrine_Manager::connection()->setCharset('utf8');
	spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
	Doctrine_Core::loadModels(dirname(__FILE__) . '/models');

	//Imposto l'autoloading delle classi del framework
	spl_autoload_register(array('Utils', 'autoload'));

	//Imposto la gestione degli errori tramite controller in modo che ritornino un'eccezione
	set_error_handler('Controller::handleError');

	//Inizializzo il framework
	$session 	= new Session();
	$request 	= new Request();
	$controller = Controller::dispatch($request, $session);

	//Resetto l'error handler a quello di default per evitare conflitti con Smarty
	restore_error_handler();

	//Eseguo il framework
	$controller->renderPage();
?>
