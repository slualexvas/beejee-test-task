<?php
	include_once 'app/core/Controller.php';
	include_once 'app/models/Model_user.php';
	include_once 'app/views/View_user.php';

	class Controller_user extends Controller
	{
		public function __construct()
		{
			$this->model = new Model_user();
			$this->view = new View_user();
		}
		public function action_login()
		{
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
				$data = $this->model->check_login_password();
			$data['is_admin'] = $this->model->is_admin();
			$this->view->generate_login($data);
		}
		public function action_logout()
		{
			$this->model->logout();
			$this->view->generate_logout();
		}
	}
?>