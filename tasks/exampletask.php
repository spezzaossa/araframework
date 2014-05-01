<?php
	require_once '_task_header.php';

	global $smarty;

	$smarty->assign('message', 'This is a message.');
	$output = $smarty->fetch('example.html');
	echo $output;
?>
