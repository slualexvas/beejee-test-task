<?php
	class View_task extends View
	{
		function sort_params_div()
		{
			return "
				<div class='sort-params'>
						<span class='main-words'>Сортировать по: </span>
						<span class='sort-var'>
							<span class='name'>Имени пользователя</span>
							<a href='/index.php?sort=username&sortdir=asc' class='btn btn-default'>A-Z</a>
							<a href='/index.php?sort=username&sortdir=desc' class='btn btn-default'>Z-A</a>
						</span>
						<span class='sort-var'>
							<span class='name'>Email</span>
							<a href='/index.php?sort=email&sortdir=asc' class='btn btn-default'>A-Z</a>
							<a href='/index.php?sort=email&sortdir=desc' class='btn btn-default'>Z-A</a>
						</span>
						<span class='sort-var'>
							<span class='name'>Status</span>
							<a href='/index.php?sort=status&sortdir=asc' class='btn btn-default'>Вверху невыполненные</a>
							<a href='/index.php?sort=status&sortdir=desc' class='btn btn-default'>Вверху выполненные</a>
						</span>
				</div>
			";
		}
		
		function pagination_div($count, $current)
		{
			$pages = '';
			
			$count_pages = (int)($count - 1) / 3 + 1;
			$i = 1;
			while ($i <= $count_pages)
			{
				$class='btn-default';
				if ($i == $current)
					$class = 'btn-info';
				$pages.="<a href='/index.php?c=task&a=index&p=$i' class='btn $class'>$i</a>";
				$i++;
			}
			$res = "
				<div class='pagination'>
					Страницы: $pages
				</div>
			";
			return ($res);
		}
		function generate_index($data = null)
		{
			$title = 'Список задач';
			include_once 'app/views/template/header.php';
			echo '<h1>Список задач</h1>
			'.$this->sort_params_div();
			
			include_once 'app/models/Model_user.php';
			$model_user = new Model_user();
			$is_admin = $model_user->is_admin();
			foreach ($data as $key=>$task)
			{
				if ($key !== 'count' && $key !== 'page' && $key !== 'is_ok')
				{
					$complete_word = 'не ';
					if ($task['status'] == '1')
						$complete_word = '';
					$edit_link = '';
					$task['text'] = str_replace("\n", '<br>', $task['text']);
					if ($is_admin == 1)
						$edit_link = "
							<a href='/index.php?c=task&a=edit&id=".$task['id']."' class='btn btn-primary'>Редактировать</a>
						";
					echo "
						<div class='container-fluid'>
							<div class='row task'>
								<img src='/uploads/task-imgs/".$task['img']."'>
								<div class='col-sm-6'>
									<h2>Задача №".$task['id']." (".$complete_word."выполнена)</h2>
									<div class='name'><b>Создал:</b> ".$task['username'].", 
									<a href='mailto:".$task['email']."'>".$task['email']."</a>
									</div>
									<div class='text'>".$task['text']."</div>
									$edit_link
								</div>
							</div>
						</div>
					";
				}
			}
			
			echo $this->pagination_div($data['count'], $data['page']);
			include_once 'app/views/template/footer.php';
		}
		
		function create_form()
		{
			$res="
			<form method='post' action='' enctype=\"multipart/form-data\">
				<div class='form-group row'>
					<label for='username' class='col-sm-4'>Ваш ник (будет отображен на сайте)</label>
					<input type='text' id='username' name='username' maxlength=100 class='col-sm-4' placeholder='Используйте латиницу и подчерки _'/>
				</div>
				<div class='form-group row'>
					<label for='email' class='col-sm-4'>Ваш email (будет отображен на сайте)</label>
					<input type='email' name='email' maxlength=100 class='col-sm-4' id='email'/>
				</div>
				<div class='form-group'>
					<label for='text'>Текст задачи</label>
					<textarea class='form-control' rows=3 placeholder='Теги и специальные символы будут убраны!' name='text' id='text'></textarea>
				</div>
				<div class='form-group'>
					<label for='img'>Изображение (jpg/gif/png, до 2МБ)</label>
					<input type='hidden' name='MAX_FILE_SIZE' value=\"".(2*1024*1024)."\" />
					<input type='file' name='file' multiple='false'/>
				</div>
				<input type='button' value='Предварительный просмотр' class='btn btn-success' onclick='preview();'>
				<input type='submit' value='Создать задачу' class='btn btn-primary'>
			</form>
			<div id='preview_div'>
			</div>
			";
			return ($res);
		}
		
		function generate_create($data = null)
		{
			$title = 'Создание задачи';
			include_once 'app/views/template/header.php';
			echo '<h1>Создание задачи</h1>';
			if ($data == null)
				echo $this->create_form();
			else
			{
				if ($data['is_ok'] == 1)
					echo "<p class='text-success'>Задача создана успешно.</p>
					<a href='/' class='btn btn-success' style='width: 20em; margin: 0.3em;'>Просмотреть список задач</a><br>
					<a href='/index.php?c=task&a=create' class='btn btn-info' style='width: 20em; margin: 0.3em;'>Создать ещё одну задачу</a><br>
					";
				else
				{
					echo "<p class='text-danger'><b>При создании задачи возникла ошибка:</b>
						".$data['error_text'].". Нажмите кнопку
						<a href='javascript:history.back()' class='btn btn-primary'>Назад</a>
						и попробуйте исправить ошибку.
					</p>";
				}
			}
			include_once 'app/views/template/footer.php';
		}
		function generate_edit($data = null)
		{
			$title = 'Редактирование задачи';
			include_once 'app/views/template/header.php';
			echo '<h1>Редактирование задачи</h1>';
			if ($data['is_ok'] == 0)
			{
				$error_text = $data['error_text'];
				echo "<p class='text-danger'><b>Ошибка:</b> $error_text</p>";
			}
			if (isset($data[0]))
				echo $this->edit_form($data);
			else
				echo "<p class='text-success'>Редактирование выполнено успешно</p>";
			include_once 'app/views/template/footer.php';
		}
		function edit_form($data)
		{
			$checked = '';
			if ($data[0]['status'] == 1)
				$checked = 'checked';
			$text = $data[0]['text'];
			$img = $data[0]['img'];
			$res="
			<form method='post'>
				<img src='/uploads/task-imgs/$img'>
				<div class='form-group'>
					<label for='text'>Текст задачи</label>
					<textarea class='form-control' rows=3 placeholder='Теги и специальные символы будут убраны!' name='text' id='text'>$text</textarea>
				</div>
				<div class='checkbox'>
					<label>
					  <input type='checkbox' name='status' $checked/> Задача выполнена
					</label>
				  </div>
				<input type='submit' value='Применить изменения' class='btn btn-primary'>
			</form>
			<div id='preview_div'>
			</div>
			";
			return ($res);
		}
	}
?>