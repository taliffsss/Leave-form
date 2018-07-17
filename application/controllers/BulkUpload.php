<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BulkUpload extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->check_if_logged_in();
		$this->load->model('M_upload','upload');
		$this->load->model('M_leave','leave');
	}

	/**
	* Import Bulk Data
	* @return Boolean
	*/
	public function import() {

		$file_data = $this->csvreader->get_array($_FILES['csv_file']['tmp_name']);

		$ids = $this->upload->getID();
		$id = $ids+1;
		
		foreach($file_data as $row)
		{
			$data[] = array(
				'name' => $row['Name'],
				'dob' => date('Y-m-d',strtotime($row['Birthdate'])),
				'position' => $row['Position'],
				'department' => $row['Department'],
				'manager' => $row['Manager'],
				'departmenthead' => $row['Department Head'],
				'created_at' => date('Y-m-d H:i:s')
			);

			$leave[] = array(
				'employee_id' => $id,
				'vacationleave' => '0/0',
				'sickleave' => '0/0',
				'birthleave' => 1
			);
			
			$users[] = array(
					'employee_id' => $id,
					'username' => $row['Username'],
					'activate' => 1,
					'email' => $row['Email'],
					'created_by' => 'test',
					'created_at' => date('Y-m-d H:i:s')
				);
			$id++;
		}
		$this->upload->bulkInsertEmployees($data);
		$this->upload->bulkInsertUser($users);
		$this->leave->batchInserleave($leave);
	}

}