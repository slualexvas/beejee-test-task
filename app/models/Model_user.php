<?php
	include_once 'app/core/Model.php';

	class Model_user extends Model
	{
		public function is_admin()
		{
			if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1)
				return (1);
			else
				return (0);
		}
		public function check_login_password()
		{
			$login = '';
			if (!empty($_POST['login']))
				$login = $_POST['login'];
			$password = '';
			if (!empty($_POST['password']))
				$password = $_POST['password'];
			if ($login == 'admin' && $password == '123')
			{
				$_SESSION['is_admin'] = 1;
				return(array('is_ok'=>1, 'error_text'=>''));
			}
			else
				return(array('is_ok'=>0, 'error_text'=>"Логин '$login' и пароль 
				<span style='color: rgba(0,0,0,0)'>'$password'</span> (выделите, чтобы прочитать пароль)
				не задают админа!"));
		}
		public function logout()
		{
			$_SESSION['is_admin'] = 0;
		}
	}
?>