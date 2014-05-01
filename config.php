<?php
	/*** CONNESSIONI AL DB ***/
	define('MYSQL_HOST', 'localhost');
	define('MYSQL_USER', 'root');
	define('MYSQL_PASS', 'uemmeuelle');
	define('MYSQL_NAME', 'ara_framework_db');

	define('MYSQL_PREFIX', 'sys');
	define('MYSQL_CONTENT_PREFIX', 'site');

	/*** MULTILINGUA ***/
	define('DEFAULT_LANG', 	'it');

	/*** PARAMETRI SITO ***/
	define('_SERVER_NAME',			'dominio.tld');
	define('_AUTORE',				'Araneus Srl');
	define('_AZIENDA',				'Azienda Cliente');
	define('_FAVICON',				'favicon.ico');
	define('_DEFAULT_TITLE',		'TITOLO DI DEFAULT');
	define('_DEFAULT_DESCRIPTION',	'DESCRIZIONE DI DEFAULT');
	define('_DEFAULT_KEYWORDS',		'KEYWORDS DI DEFAULT');
	define('_MAIN_MAIL',			'supporto@araneus.it');
	define('HOME_PAGE',				'home');

	define('_TEST_BASE_PATH',		'dominio.tld/');
	define('_PRODUCTION_BASE_PATH',	'');

	/*** LAYOUT ***/
	$GLOBALS['TEMPLATE'] = 'default';

	/*** SEO (sovrascrive titoli, descrizioni e parole chiave) ***/
	define('IS_SEO_ENABLED', 		TRUE);

	/*** COOKIE/AUTH ***/
	define('COOKIE_NAME', 		'http___'._SERVER_NAME.'_auth');
	define('COOKIE_TIMELIFE', 	86400);
	define('COOKIE_ADMIN',		'http___'._SERVER_NAME.'_admin');

	/***
	 * Se impostato a TRUE gli errori (404) non verranno mostrati ma l'utente
	 * sarà reindirizzato alla pagina impostata con SOFT_ERRORS_REDIRECT.
	 * Questo è un redirect quindi potete inserire anche parametri ecc.
	 ***/
	define('SOFT_ERRORS', TRUE);
	define('SOFT_ERRORS_REDIRECT', 'home');

	define('DEBUG_MODE', TRUE);
	ini_set('display_errors', DEBUG_MODE);

	define('MEDIA_FOLDER', 'resource/files/');

	/*** PARAMETRI FACEBOOK ***/
//	define('FACEBOOK_APP_ID', '');
//	define('FACEBOOK_SECRET', '');
//	define('FACEBOOK_APP_URL', '');
//	define('FACEBOOK_REDIRECT_URL', '');

	/*** PARAMETRI PAYPAL ***/
//	define('PAYPAL_USERNAME', '');
//	define('PAYPAL_PASSWORD', '');
//	define('PAYPAL_SIGNATURE', '');
//	define('PAYPAL_APPID', '');

//	define('PAYPAL_SANDBOX_USERNAME', '');
//	define('PAYPAL_SANDBOX_PASSWORD', '');
//	define('PAYPAL_SANDBOX_SIGNATURE', '');
//	define('PAYPAL_SANDBOX_APPID', '');


	/*** FILE JS ***/
	$js_files = array(
		'jquery-1.9.1.js',
		'jquery-ui-1.10.3.custom.min.js',
		'jquery.datapicker.it.js',
		'html5.js',
		'bootstrap.min.js',
		'moment-with-langs.min.js',
		'araForm.js',
		'custom.js'
	);

	/*** FILE CSS ***/
	$css_files = array(
		'reset.css',
		'jquery-ui-1.10.3.custom.min.css',
		'bootstrap.min.css',
		'style.css'
	);

	/*** FILE JS ***/
	$js_admin_files = array(
		'html5.js',
		'admin.jquery-2.0.3.min.js',
		'admin.jquery-ui-1.10.3.custom.min.js',
		'bootstrap.min.js',
		'jquery.dataTables.min.js',
		'dataTables-Bootstrap.js',
		'dataTables.reloadAjax.js',
		'fineuploader-3.0.min.js',
		'jquery.Jcrop.js',
		'jquery.ba-dotimeout.min.js',
		'moment-with-langs.min.js',
		'araForm.js',
		'admin.js'
	);

	/*** FILE CSS ***/
	$css_admin_files = array(
		'reset.css',
		'bootstrap.min.css',
		'jquery.Jcrop.css',
		'admin.jquery-ui-1.10.3.custom.min.css',
		'dataTables-Bootstrap.css',
		'fineuploader.css',
		'jquery.Jcrop.css',
		'admin.css'
	);

	define('_BASE_HREF', 'http://'.$_SERVER['SERVER_NAME'].'/'.((DEBUG_MODE) ? _TEST_BASE_PATH : _PRODUCTION_BASE_PATH));
?>
