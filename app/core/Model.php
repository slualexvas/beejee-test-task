<?php
	class Model
	{
		public function get_data()
		{
			return (null);
		}
		
		public function query($link, $string)
		{
			$query_result = $link->query($string);
			if ($query_result === FALSE)
				throw new Exception(
					'<p>MySQL error: 
					'.$this->connection->error.'</p>
					<p><b>Query:</b> 
					'.$str_query.'</p>'
				);
			return ($query_result);
		}
		
		public function query_select($link, $string)
		{
			$query_result = $this->query($link, $string);
			$data_arr = array();
			while ($row = $query_result->fetch_assoc())
			{
				$data_arr[] = $row;
			}
			$query_result->free();
			return $data_arr;
		}
	}
?>