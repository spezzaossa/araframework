<?php

abstract class Widget
{
	protected $smarty	= NULL;
	protected $session	= NULL;
	protected $request	= NULL;
	protected $params	= NULL;

	final public function __construct($params, $session, $request) {
		$this->params	= $params;
		$this->session 	= $session;
		$this->request 	= $request;
		$this->smarty 	= new Smarty();
	}

	public static function get($widgetName, $params = null)
	{
		$widgetClass = ucfirst($widgetName).'Widget';

		if (!class_exists($widgetClass))
		{
			try
			{
				include(dirname(__FILE__)."/../widgets/$widgetName/widget.php");
			}
			catch (ErrorException $e)
			{
				return '';
			}
		}

		global $session;
		global $request;
		$widget = new $widgetClass($params, $session, $request);
		if(!($widget instanceof Widget)) return '';

		/*** Imposto Smarty (viene impostato qui nel caso che venga invocato smarty direttamente nei controller per visualizzare pezzi di HTML in AJAX) ***/
		if(DEBUG_MODE) $widget->smarty->force_compile = TRUE;
		$widget->smarty->template_dir 	= realpath(dirname(__FILE__).'/../tpl');
		$widget->smarty->compile_dir 	= realpath(dirname(__FILE__).'/../compiled');

		$widget->execute();
		return $widget->smarty->fetch(dirname(__FILE__)."/../widgets/$widgetName/widget.html");
	}

	abstract protected function execute();
}

?>