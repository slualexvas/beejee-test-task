<?php
	class View_user extends View
	{
		function login_form()
		{
			return ("
				<form class='form-inline' method='post'>
					<div class='form-group'>
						<label for='login'>Логин администратора</label>
						<input type='text' class='form-control' name='login'/>
					</div>
					<div class='form-group'>
						<label for='password'>Пароль администратора</label>
						<input type='password' class='form-control' name='password'/>
					</div>
					<input type='submit' class='btn btn-primary' value='Войти'/>
				</form>
			");
		}
		function generate_login($data)
		{
			$title = 'Вход администратора';
			include_once 'app/views/template/header.php';
			
			echo '<h1>Вход администратора</h1>';
			if (!isset($data['is_ok']))
				echo $this->login_form();
			else
			{
				if ($data['is_ok'] == 1)
					echo "<p class='text-success'>Вы успешно залогинились!</p>";
				else
					echo "<p class='text-danger'><b>Ошибка:</b> ".$data['error_text'].". Нажмите кнопку 
					<a href='javascript:history.back()' class='btn btn-primary'>Назад</a>
						и попробуйте исправить ошибку.
				</p>";
			}

			include_once 'app/views/template/footer.php';
		}
		function generate_logout()
		{
			$title = 'Выход из аккаунта администратора';
			include_once 'app/views/template/header.php';
			echo "<p class='text-success'>Вы успешно разлогинились.</p>";
			include_once 'app/views/template/footer.php';
		}
	}
?>