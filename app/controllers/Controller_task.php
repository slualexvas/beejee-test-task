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
			$id = 0;
			$limit1 = 0;
			$page = 1;
			if (!empty($_GET['p']))
				$page = (int)$_GET['p'];
			$limit1 = ($page - 1) * 3;
			$order_by = 'username';
			if (!empty($_GET['sort']))
				$order_by = $_GET['sort'];
			$order_direction = 'asc';
			if (!empty($_GET['sortdir']))
				$order_direction = $_GET['sortdir'];
			$data = $this->model->get_data($id, $limit1, $order_by, $order_direction);
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