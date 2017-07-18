<?php
	include_once 'app/core/Model.php';
	include_once 'app/models/Model_user.php';

	class Model_task extends Model
	{
		private function connect_to_db()
		{
			$this->db_link = new mysqli(
				'mysql302.1gb.ua',
				'gbua_slutsky_tz',
				'933002d36io',
				'gbua_slutsky_tz'
			);
			if ($this->db_link === FALSE)
				throw new Exception(
					'Database connecting error! (
					'.$this->db_link->connect_errno.')
					.$this->db_link->connect_error'
				);
		}
		public function __construct()
		{
			$this->connect_to_db();
			$this->query($this->db_link, "SET NAMES UTF8");
		}
		private $db_link;
		
		public function get_data($id=0, $limit1=0, $order_by='username', $order_direction='asc')
		{
			if ((int)$id < 0)
				$id = 0;
			if ((int)$limit < 0)
				$limit = 0;
			if (!in_array($order_by, array('username', 'email', 'status')))
				$order_by = 'username';
			if (!in_array($order_direction, array('asc', 'desc')))
				$order_direction = 'asc';
			$where_string = '';
			$order_string = '';
			if ((int)$id != 0)
				$where_string = ' WHERE `id` = '.(int)$id;
			else
				$order_string = " ORDER BY `{$order_by}` $order_direction LIMIT {$limit1},3";
			$query_string = "SELECT * FROM `tasks`".$where_string.$order_string;
			
			$res = $this->query_select($this->db_link, $query_string);
			if (!isset($res[0]))
				return (array('is_ok'=>0, 'error_text'=>"ID = '$id' не существует в базе данных!"));
			$raw_count = $this->query_select($this->db_link, "SELECT COUNT(*) as `count` FROM `tasks`");
			$res['count'] = $raw_count[0]['count'];
			$res['is_ok'] = 1;
			return ($res);
		}
		public function check_post_create()
		{
			$_POST['text'] = strip_tags($_POST['text']);
			$_POST['text'] = trim($_POST['text']);
			if (mb_strlen($_POST['text']) < 5)
				return (array('is_ok'=>0, 'error_text'=>'Текст задачи не соответствует правилам.
				Кроме тегов и пробелов, должно быть хотя бы 5 букв текста'));
			if (!preg_match("/^[a-z]{1,}+[a-z_]{0,99}$/iu", $_POST['username']))
				return (array('is_ok'=>0, 'error_text'=>'Ник пользователя не соответствует правилам.
				Ник может содержать от 1 до 100 латинских букв и подчерки _.
				Первый символ --- обязательно латинская буква.'));
			else if (!preg_match("/.+@.+\..+/iu", $_POST['email']))
				return (array('is_ok'=>0, 'error_text'=>'Email не соответствует правилам.
				Email должен содержать знак @ и точку.'));
				//Мотивация для настолько слабой проверки: https://habrahabr.ru/post/175375
			else if (empty($_FILES['file']))
				return (array('is_ok'=>0, 'error_text'=>'Неопознанная ошибка загрузки файла'));
			else if ($_FILES['file']['error']==4)
				return (array('is_ok'=>0, 'error_text'=>'Файл не выбран'));
			else if ($_FILES['file']['error']==2 || $_FILES['file']['size'] > 2*1024*1024)
				return (array('is_ok'=>0, 'error_text'=>'Размер файла должен быть не больше 2МБ'));
				//Немного неудачное указание константы 2*1024*1024 в двух отдельных местах
				//Обычно в своих проектах я записываю подобную константу в файл params.php
				//Туда же пишу параметры доступа к БД и тому подобную инф-ю.
				//Но здесь параметры доступа к БД использовались только в одном месте,
				//и показалось слишком мелко ради одной константы создавать и использовать конф.файл
			$ext=substr(strrchr($_FILES['file']['name'], '.'), 1);
			if (!in_array($ext, array('jpg', 'gif', 'png')))
				return (array('is_ok'=>0, 'error_text'=>'Расширение файла "'.$ext.'" (должно быть jpg, gif или png)'));
				// Дословно в тех задании было "Требования к изображениям - формат JPG/GIF/PNG"
				// Мне кажется, что имелось в виду всё же "jpg/gif/png", т.е. маленькие буквы
				// В рабочей обстановке я бы переспросил
			return (array('is_ok'=>1, 'error_text'=>''));
		}
		
		public function work_with_image_for_checked_task()
		{
			$id = $this->db_link->insert_id;
			switch($_FILES['file']['type'])
			{
				case 'image/gif': $im = imagecreatefromgif($_FILES['file']['tmp_name']); break;
				case 'image/png': $im = imagecreatefrompng($_FILES['file']['tmp_name']); break;
				case 'image/jpeg': $im = imagecreatefromjpeg($_FILES['file']['tmp_name']); break;
			}
			list($w, $h) = getimagesize($_FILES['file']['tmp_name']);
			$k = $w / 320;
			$new_h = ceil($h / $k);
			$im1 = imagecreatetruecolor(320, $new_h);
			imagecopyresampled($im1,$im,0,0,0,0,320,$new_h,imagesx($im),imagesy($im));
			$filename = "{$id}.jpg";
			$path = "uploads/task-imgs/$filename";
			imagejpeg($im1, $path, 100);
			imagedestroy($im);
			imagedestroy($im1);
			$query_string = "UPDATE `tasks` SET `img`='$filename' WHERE `id`='$id'";
			$query_res = $this->query($this->db_link, $query_string);
			if ($query_res === FALSE)
				return (array('is_ok'=>0, 'error_text'=>'Не удалось прикрепить картинку к задаче!'));
			return (array('is_ok'=>1, 'error_text'=>''));
		}
		
		public function create_checked_task()
		{
			$text = htmlspecialchars($_POST['text']);
			$text = $this->db_link->real_escape_string($text);
			
			$username = $_POST['username'];
			$email = $_POST['email'];
			
			$query_string = "
				INSERT INTO `tasks`(`text`, `username`, `email`, `status`)
				VALUES('$text','$username','$email',0)
			";
			$query_res = $this->query($this->db_link, $query_string);
			if ($query_res === FALSE)
				return (array('is_ok'=>0, 'error_text'=>'Не удалось добавить запись в базу данных'));
			else
				return ($this->work_with_image_for_checked_task());
		}
		public function create_task()
		{
			$res = array('is_ok'=>1, 'error_text'=>'');
			$res = $this->check_post_create();
			if ($res['is_ok'] == 0)
				return ($res);
			$res = $this->create_checked_task();
			return ($res);
		}
		public function get_data_for_edit($id)
		{
			$data = array('is_ok'=>1, 'error_text'=>'');
			$model_user = new Model_user();
			$is_admin = $model_user->is_admin();
			if ($is_admin == 0)
				return (array('is_ok'=>0, 'error_text'=>'Вы не админ!'));
			$data = $this->get_data($id);
			return ($data);
		}
		public function edit_data()
		{
			$model_user = new Model_user();
			$is_admin = $model_user->is_admin();
			if ($is_admin == 0)
				return (array('is_ok'=>0, 'error_text'=>'Вы не админ!'));
			
			$_POST['text'] = strip_tags($_POST['text']);
			$_POST['text'] = trim($_POST['text']);
			if (mb_strlen($_POST['text']) < 5)
				return (array('is_ok'=>0, 'error_text'=>'Текст задачи не соответствует правилам.
				Кроме тегов и пробелов, должно быть хотя бы 5 букв текста'));
			//Здесь нарушение принципа DRY. Его можно было бы избежать, выделив проверку text в отдельную функцию.
			//Но снизилась бы понятность проекта, как мне кажется.
			$text = htmlspecialchars($_POST['text']);
			$text = $this->db_link->real_escape_string($text);
			
			$status = 0;
			if (isset($_POST['status']) && $_POST['status'] === 'on')
				$status = 1;
			$id = (int)$_GET['id'];
			$query_string = "UPDATE `tasks` SET `text`='$text', `status`=$status WHERE `id`='$id'";
			$query_res = $this->query($this->db_link, $query_string);
			return (array('is_ok'=>1, 'error_text'=>''));
		}
	}
?>