<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.widget.php
 * Type:     function
 * Name:     widget
 * Purpose:  insert widget
 * -------------------------------------------------------------
 */
function smarty_function_widget($params, Smarty_Internal_Template $template)
{
	return Widget::get($params['name'], $params);
}
?>