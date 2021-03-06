<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_employee extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	/**
	* Use for activity Logs
	* Get New Insert Id
	* @return Int
	*/
	public function getNewInsert() {
		$where = array('created_at' => date('Y-m-d H:i:s'));
		$this->db->select('id');
		$this->db->where($where);
		$res = $this->db->get('employee');
		return $res->result();
	}

	public function insertNew($data) {
		return $this->db->insert('employee',$data);
	}

	/**
	* Get Leave Balance
	* @return String
	* @return Int
	*/
	public function getLeave($clause) {
		$this->db->select("e.name, u.employee_id, l.vacationleave, l.sickleave, l.birthleave, u.username, e.id, u.active, SUBSTRING_INDEX(SUBSTRING_INDEX(e.name, ' ', 2 ),' ',1) AS firstname, SUBSTRING_INDEX(SUBSTRING_INDEX(e.name, ' ', -1 ),' ',2) AS lastname, YEAR(l.updated_at) as year");
		$this->db->from('employee e');
		$this->db->join('leavebalance l','l.employee_id = e.id');
		$this->db->join('users u','u.employee_id = e.id');
		$this->db->where("e.manager = '$clause' OR e.department = '$clause'");
		$res = $this->db->get();
		return $res->result();

	}

	public function getEmployeesData($id) {
		$data = array();
		$where = array('employee_id' => $id);
		$this->db->where($where);
		$res = $this->db->get('users');
		if($res->num_rows() > 0){
			$data = $res->row_array();
		}
		$res->free_result();
		return $data;
	}

	public function getUpdateLeaveYear($id = NULL) {
		$where = array('employee_id' => $id);
		$data = array();
		$this->db->select('YEAR(`updated_at`) as year');
		if($id != NULL) {
			$this->db->where($where);
		}
		$res = $this->db->get('leavebalance');
		if($res->num_rows() > 0){
			$data = $res->row_array();
		}
		$res->free_result();
		return $data;
	}

	public function vacationLeaveBalance($total) {
		$where = array('employee_id' => $this->session->userdata('empid'));
		$data = array('vacationleave' => $total);
		$this->db->where($where);
		return $this->db->update('leavebalance',$data);
	}

	public function sickLeaveBalance($total) {
		$where = array('employee_id' => $this->session->userdata('empid'));
		$data = array('sickleave' => $total);
		$this->db->where($where);
		return $this->db->update('leavebalance',$data);
	}

	public function birthdayLeave() {
		$where = array('employee_id' => $this->session->userdata('empid'));
		$data = array('birthleave' => 0);
		$this->db->where($where);
		return $this->db->update('leavebalance',$data);
	}

	/**
	* @param Int Employee Id
	* @return Boolean
	*/
	public function getMyleave() {
		$data = array();
		$where = array('employee_id' => $this->session->userdata('empid'));
		$this->db->where($where);
		$res = $this->db->get('leavebalance');
		if($res->num_rows() > 0){
			$data = $res->row_array();
		}
		$res->free_result();
		return $data;
	}

	/**
	* Get Birthday of the month
	* @return String
	* @return Date
	*/
	public function getdob($dept) {

		$this->db->where("MONTH(dob) = MONTH(NOW()) AND department = '$dept'");
		$this->db->order_by('DAY(dob)', 'ASC');
		$res = $this->db->get('employee');
		return $res->result();

	}

	public function getRejected() {

		$where = array('employee_id' => $this->session->userdata('empid'));
		$this->db->where($where);
		$res = $this->db->get('leavehistory');
		return $res->result();

	}

	/**
	* Get Event
	* @param Date
	* @return String
	*/
	public function getEvents($start, $end, $clause) {

		$this->db->select('u.username, l.*');
		$this->db->from('users u');
		$this->db->join('leavehistory l','u.employee_id = l.employee_id','LEFT');
		$this->db->join('employee e','u.employee_id = e.id','LEFT');
		$this->db->where("l.start >= '$start' AND l.end <= '$end' AND l.active = 1 AND (e.manager = '$clause' OR e.department = '$clause')");
		$res = $this->db->get();
		return $res->result();

	}

	/**
	* Insert Event
	*/
	public function addEvents() {

		return $this->db->insert('leavehistory',$data);

	}

	/**
	* Update Event
	* @param Int $id
	*/
	public function updateEvents($id) {

		$this->db->where('id',$id);
		return $this->db->update('leavehistory',$data);

	}

}
?>
