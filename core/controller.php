<?php

	/**
	 * Interfaccia per definire i controller che "sovrascrivono" il SEO delle pagine.
	 * getSEO deve ritornare un oggetto SysSeo.
	 */
	interface SEO
	{
		/**
		 * @returns SysSeo relativo al controller
		 */
		function getSEO();
	}

	/**
	 * Classe astratta che definisce il controller dell'applicazione.
	 * Viene implementata dai controller delle singole pagine ed espone un metodo statico per il dispacht delle richieste.
	 * @author Simone
	 */
	abstract class Controller {
		/** @var Request */
		protected $request 	= NULL;
		/** @var Session */
		protected $session 	= NULL;
		/** @var string */
		protected $page		= NULL;
		/** @var string */
		protected $pagetree	= NULL;
		/** @var string */
		protected $url		= NULL;
		/** @var Smarty */
		protected $smarty	= NULL;

		protected $meta_title = NULL;
		protected $meta_description = NULL;
		protected $meta_keywords = NULL;

		/**
		 * Metodo astratto. Contiene il codice computazionale del singolo controller.
		 */
		abstract protected function execute();

		/**
		 * Costruttore. Non puo' essere implementato dalle sottoclassi.
		 * @param Request $request L'oggetto Request della singola richiesta per la pagina.
		 * @param string $page La pagina richiesta
		 */
		final public function __construct(Request $request, Session $session, $page) {
			$this->request 	= $request;
			$this->session 	= $session;
			$this->page 	= $page;
			$this->smarty 	= new Smarty();
		}

		/**
		 * Metodo statico. Inoltra la richiesta al controller della pagina desiderata.
		 * @param Request $request L'oggetto Request della singola richiesta per la pagina.
		 */
		public static function dispatch(Request $request, Session $session) {
			$page = $request->getPage();

			if ($page == 'sitemap.xml') {
				header('Content-Type: text/xml');
				exit(Controller::generateXMLSitemap());
			}

			if (substr($page, -5) ==  '.html') $page = substr($page, 0 , -5);
			if (substr($page, -4) ==  '.htm') $page = substr($page, 0 , -4);

			$page_slug = SysSlugsTable::getInstance()->createQuery('a')
					->where('a.slug = ?', $page)
					->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

			if($page_slug)
				$page = $page_slug["page_url"];

			/*
			 * esplodo l'url per /
			 * prendo la prima parte e la esplodo per -
			 * scorro l'array ottenuto generando un page tree con i nomi delle classi che trovo
			 * tutto quello che non è una pagina lo considero un parametro
			 */
			$url_slices = explode('/', $page);
			$url_slices = explode('-', $url_slices[0]);

			$page_tree = array();
			$param_number = 1;

			$detectedLang = false;
			foreach ($url_slices as $page) {
				if ($detectedLang) {
					$page_alias = SysAliasesTable::getInstance()->createQuery('a')
							->leftJoin('a.Page')
							->where('a.value = ?', $page)
							->andWhere('a.id_language = ?', $detectedLang)
							->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);
				} else {
					// Controllo se esiste un'alias per la lingua corrente con
					// il nome che stiamo visitando, in caso non cambia la lingua
					// di visualizzazione.
					$page_alias = SysAliasesTable::getInstance()->createQuery('a')
							->leftJoin('a.Page')
							->leftJoin('a.Language l')
							->where('a.value = ?', $page)
							->andWhere('a.Language.code = ?', $session->getCurrentLang())
							->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

					if (!$page_alias)
					{
						$page_alias = SysAliasesTable::getInstance()->createQuery('a')
								->leftJoin('a.Page')
								->leftJoin('a.Language l')
								->where('a.value = ?', $page)
								->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

					if($page_alias) {
							$detectedLang = $page_alias['Language']['id'];
							$session->setCurrentLang($page_alias['Language']['code']);
					}
				}
				}

				if($page_alias) {
					$page = $page_alias['Page']['name'];
				} else {
					//@TODO Mostrare pagina 404 con messaggio opportuno
					//ma anche no
//					header("Status: 404 Not Found");
//					header("HTTP/1.0 404 Not Found");
//					$controller = new errorsController($request, $session, 404);
//					return $controller;
//					die($page . ' non esiste.');
				}

				if(!is_dir(dirname(__FILE__)."/../pages/".implode('/',$page_tree)."/$page")) {
					$request->setParam('param_'.$param_number, $page);
					$param_number++;
				} else {
					$page_tree[] = $page;
				}
			}

			try
			{
				include(dirname(__FILE__)."/../pages/".implode('/',$page_tree)."/controller.php");
				$controllerClass = ucfirst(end($page_tree)).'Controller';
			}
			catch (ErrorException $e)
			{
				if (!SOFT_ERRORS)
				{
					header("Status: 404 Not Found");
					header("HTTP/1.0 404 Not Found");
					return new errorsController($request, $session, 404);
				}
				else
				{
					header('Location: '._BASE_HREF.SOFT_ERRORS_REDIRECT);
					exit();
				}
			}

			$controller = new $controllerClass($request, $session, end($page_tree));
			if(!($controller instanceof Controller))
			{
				return new errorsController($request, $session, 404);
				die("La classe $controllerClass non estende Controller"); 	//@TODO Mostrare pagina 404 con messaggio opportuno
			}

			$controller->pagetree = implode('/', $page_tree);
			$controller->url = implode('-', $page_tree);

			//controlla se esistono le cartelle del page tree all'interno della cartella css e quella js. Se, al suo interno è presente qualche file .css o .js, allora assegna le variabili che verranno richiamate nel main per la minificazione
			$css_page_tree_dir = dirname(__FILE__)."/../resource/css/".implode('/',$page_tree)."/";
			$js_page_tree_dir = dirname(__FILE__)."/../resource/js/".implode('/',$page_tree)."/";
			if(is_dir($css_page_tree_dir)) {
				$array_css_files = Utils::getFilesFromDir($css_page_tree_dir, 'css');
				if(count($array_css_files) > 0)
					$controller->smarty->assign('min_tree_css', implode('-',$page_tree));
			}
			if(is_dir($js_page_tree_dir)) {
				$array_js_files = Utils::getFilesFromDir($js_page_tree_dir, 'js');
				if(count($array_js_files) > 0)
					$controller->smarty->assign('min_tree_js', implode('-',$page_tree));
			}

			/*** Imposto Smarty (viene impostato qui nel caso che venga invocato smarty direttamente nei controller per visualizzare pezzi di HTML in AJAX) ***/
			if(DEBUG_MODE) $controller->smarty->force_compile = TRUE;
			$controller->smarty->template_dir 	= realpath(dirname(__FILE__).'/../tpl');
			$controller->smarty->compile_dir 	= realpath(dirname(__FILE__).'/../compiled');
			return $controller;
		}

		public function renderPage() {

			/*** Recupero i meta per il seo ***/
			$meta_title			= _DEFAULT_TITLE;
			$meta_description 	= _DEFAULT_DESCRIPTION;
			$meta_keywords 		= _DEFAULT_KEYWORDS;

			if (IS_SEO_ENABLED) {
				$metatag = false;

				if (in_array('SEO', class_implements(get_class())))
					$metatag = $this->getSEO();

				// Se non trova metatag nel controller corrente, recupera quelli della pagina
				if (!$metatag)
				{
					$metatag = SysSeoTable::getInstance()->createQuery('p')
							->leftJoin('p.Language')
							->leftJoin('p.Page')
							->where('p.Language.code = ?', $this->session->getCurrentLang())
							->andWhere('p.Page.name = ?', $this->page)
							->fetchOne();

					// Se non trova metatag nella lingua corrente, recupera quelli della lingua di default
					if (!$metatag) {
						$metatag = SysSeoTable::getInstance()->createQuery('p')
								->leftJoin('p.Language')
								->leftJoin('p.Page')
								->where('p.Language.code = ?', DEFAULT_LANG)
								->andWhere('p.Page.name = ?', $this->page)
								->fetchOne();
					}
				}

				if ($metatag) {
					$meta_title			= $metatag->meta_title;
					$meta_description	= $metatag->meta_description;
					$meta_keywords		= $metatag->meta_keywords;
				}
			}

			$this->meta_title		= $meta_title;
			$this->meta_description	= $meta_description;
			$this->meta_keywords	= $meta_keywords;

			/*** Eseguo i calcoli ***/
			$asynch = $this->execute();

			if(!$asynch) {
				/*** Imposto il path per il template ***/
				if(!file_exists(dirname(__FILE__).'/../tpl/'.$GLOBALS['TEMPLATE'].'/template.html'))
					die("Necessario template.html: ".$GLOBALS['TEMPLATE']); 							//@TODO Mostrare pagina 404 con messaggio opportuno
				$template = realpath(dirname(__FILE__).'/../tpl/'.$GLOBALS['TEMPLATE'].'/template.html');

				/*** Imposto il path per il contenuto centrale ***/
				if(!file_exists(dirname(__FILE__).'/../pages/'.$this->pagetree.'/view.html'))
					die("Necessario view.html: $this->page");											//@TODO Mostrare pagina 404 con messaggio opportuno
				$content = realpath(dirname(__FILE__).'/../pages/'.$this->pagetree.'/view.html');

				/*** Imposto Smarty ***/
				if(DEBUG_MODE) $this->smarty->force_compile = TRUE;
				$this->smarty->template_dir 	= realpath(dirname(__FILE__).'/../tpl');
				$this->smarty->compile_dir 		= realpath(dirname(__FILE__).'/../compiled');

				/*** Assegno le variabili ***/
				$this->smarty->assign('meta_title',			$this->meta_title);
				$this->smarty->assign('meta_description',	$this->meta_description);
				$this->smarty->assign('meta_keywords',		$this->meta_keywords);
				$this->smarty->assign('meta_author', 		_AUTORE);
				$this->smarty->assign('favicon', 			_FAVICON);
				$this->smarty->assign('request', 			$this->request->getParams());
				$this->smarty->assign('logged', 			$this->session->isLogged());
				$this->smarty->assign('admin_logged',		$this->session->isAdminLogged());
				$this->smarty->assign('lang',				$this->session->getCurrentLang());
				$this->smarty->assign('page',				$this->page);
				$this->smarty->assign('pagetree',			$this->pagetree);
				$this->smarty->assign('query_string',		$this->request->getQueryString());

				$this->smarty->assign('template', 'file:'.$template);
				$this->smarty->assign('content', 'file:'.$content);

				if($this->request->getParam('err')) {
					$this->smarty->assign('error', $this->request->getParam('err'));
				} else {
					$this->smarty->assign('error', '');
				}

				/*
				 * Recupero contenuto della pagina da database, nella lingua
				 * corrente, è stato spostato qua perch� comunque andrà fatto
				 * nel 99% delle pagine.
				 */
				$content = SiteContentsTable::getInstance()->createQuery('a')
						->leftJoin('a.Language')
						->leftJoin('a.Page')
						->where('a.Language.code = ?', $this->session->getCurrentLang())
						->andWhere('a.Page.name = ?', $this->page)
						->orderBy('a.sort_order ASC')
						->fetchArray();

				for($i = 0; $i < count($content); $i++)
				{
					$matches = null;
					preg_match_all('#(<a.*href="mailto:[^@]+@[-a-z0-9.]+">.*?</a>|[a-zA-Z0-9]+?@[-a-z0-9.]+)#', $content[$i]['content'], $matches);
					array_shift($matches);

					$replace = array();
					foreach($matches[0] as $match)
						$replace[] = '<script type="text/javascript">document.write(ROT47(\''.Utils::str_rot47($match).'\'));</script>';

					$content[$i]['content'] = str_replace($matches[0], $replace, $content[$i]['content']);

					$this->smarty->assign('page_content_'.$content[$i]['sort_order'], $content[$i]);
				}

				include(dirname(__FILE__).'/../custom.php');

				/*** Mostro la pagina ***/
				$this->smarty->display('main.html');
			} else {
				echo json_encode($asynch);
			}
		}

		public static function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
		{
			// error was suppressed with the @-operator
			if (0 === error_reporting()) {
				return false;
			}

			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		}

		public static function generateXMLSitemap()
		{
			$xml = new XMLWriter();
			$xml->openMemory();
			$xml->startDocument('1.0', 'UTF-8');
			$xml->startElement('urlset');
			$xml->writeAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');
			$xml->writeAttribute('xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
			$xml->writeAttribute('xsi:schemaLocation','http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

			$pages = SysPagesTable::getInstance()->findAll();

			foreach ($pages as $page)
			{
				$xml->startElement('url');
				$xml->writeElement('loc', 'http://www.'._SERVER_NAME.'/'.$page->TreeLink);
				$xml->writeElement('changefreq', 'always');
				$xml->writeElement('priority', '1.00');
				$xml->endElement();
			}

			$slugs = SysSlugsTable::getInstance()->findAll();

			foreach ($slugs as $slug)
			{
				$xml->startElement('url');
				$xml->writeElement('loc', 'http://www.'._SERVER_NAME.'/'.$slug->slug);
				$xml->writeElement('changefreq', 'always');
				$xml->writeElement('priority', '1.00');
				$xml->endElement();
			}

			$xml->endElement();
			$xml->endDocument();

			return $xml->flush();
		}
	}
?>
