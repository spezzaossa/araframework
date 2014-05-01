<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.rot47.php
 * Type:     function
 * Name:     rot47
 * Purpose:  insert rot47
 * -------------------------------------------------------------
 */
function smarty_function_rot47($params, Smarty_Internal_Template $template)
{
	$rot47 = Utils::str_rot47($params['string']);
	return '<script type="text/javascript">document.write(ROT47("'.$rot47.'"));</script>';
}
?>