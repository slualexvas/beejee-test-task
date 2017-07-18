<?php
	ini_set('display_errors', 1);
	include_once('app/core/Route.php');
	
	
	try
	{
		if (session_start() === FALSE)
			throw new Exception("Сессия не стартовала! Работать от имени администратора не получится!");
		Route::start();
	}
	catch(Exception $e)
	{
		include_once 'app/views/template/header.php';
		echo '<p class="text-danger"><b>Error:</b> '.$e->getMessage().'</p>';
		include_once 'app/views/template/footer.php';
	}
?>