<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?php echo $title ?></title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.css" rel="stylesheet">
	<!-- Custom stylesheet -->
	<link href="/css/style.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  <nav class='navbar navbar-default'>
	<div class='container-fluid'>
		<div class='navbar-header'>
			<a class='navbar-brand' href='/'>
				Задачник
			</a>
		</div>
		<div class='collapse navbar-collapse' id='bs-example-navbar-collapse-1'>
			<ul class="nav navbar-nav">
				<li><a href='/index.php?c=task&a=create'>Создать задачу</a></li>
				<?php
					include_once 'app/models/Model_user.php';
					$model_user = new Model_user();
					if ($model_user->is_admin() == 0)
						echo "
							<li><a href='/index.php?c=user&a=login'>Войти как администратор</a></li>
							";
					else
						echo "
							<li><a href='/index.php?c=user&a=logout'>Выйти из аккаунта администратора</a></li>
							";
				?>
			</ul>
		</div>
	</div>
  </nav>