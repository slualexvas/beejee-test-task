<?php
	include_once 'app/core/Controller.php';
	include_once 'app/models/Model_task.php';
	include_once 'app/views/View_task.php';
	
	class Controller_task extends Controller
	{
		function 	__construct()
		{
			$this->view = new View_task();
			$this->model = new Model_task();
		}
		public function action_index()
		{
			$page = $_GET['p'] ?: 1;
			$data = $this->model->get_data(
                0,
                ($page - 1) * 3,
                $_GET['sort'] ?: 'username',
                $_GET['sortdir'] ?: 'asc'
            );
			$data['page'] = $page;
			$this->view->generate_index($data);
		}
		
		public function action_create()
		{
			$data = null;
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
				$data = $this->model->create_task();
			$this->view->generate_create($data);
		}
		
		public function action_edit()
		{
			$data = null;
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
				$data = $this->model->edit_data();
			else
				$data = $this->model->get_data_for_edit($_GET['id']);
			$this->view->generate_edit($data);
		}
	}
?>