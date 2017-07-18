<?php
	class Route
	{
		static function start()
		{
			$controller_name = 'task';
			$action_name = 'index';
			
			if (!empty($_GET['c']))
				$controller_name = $_GET['c'];
			if (!empty($_GET['a']))
				$action_name = $_GET['a'];
			
			$controller_name = 'Controller_'.$controller_name;
			$controller_file = 'app/controllers/'.$controller_name.'.php';
			if (file_exists($controller_file))
			{
				include_once $controller_file;
			}
			else
			{
				throw new Exception('Controller not found!');
			}
			
			$controller = new $controller_name;
			$action_name = 'action_'.$action_name;
			
			if(method_exists($controller_name, $action_name))
			{
				$controller->$action_name();
			}
			else
			{
				throw new Exception('Action not found!');
			}
		}
	}
?>