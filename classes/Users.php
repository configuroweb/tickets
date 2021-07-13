<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function load_users(){
		$qry = $this->conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM `users` order by concat(firstname,' ',lastname) asc");
		$data = array();
			while($row = $qry->fetch_assoc()){
				$row['avatar'] = validate_image($row['avatar']);
				$data[] = $row;
			}	
			$resp['status']='success';
			$resp['data'] = $data;
			return json_encode($resp);
	}
	public function load_customers(){
		$qry = $this->conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM `customers` order by concat(firstname,' ',lastname) asc");
		$data = array();
			while($row = $qry->fetch_assoc()){
				$row['avatar'] = validate_image($row['avatar']);
				$data[] = $row;
			}	
			$resp['status']='success';
			$resp['data'] = $data;
			return json_encode($resp);
	}
	function create_customer(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
			if(isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar']) && !empty($id))
					unlink('../'.$_SESSION['userdata']['avatar']);
			$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
			if($move){
				$data .=" , avatar = '{$fname}' ";
				
			}
		}
		$chk = $this->conn->query("SELECT * FROM customers where username = '{$username}' ")->num_rows;
		if($chk > 0){
			return 3;exit;
		}
		$qry = $this->conn->query("INSERT INTO customers set {$data}");

		if($qry){
			$this->settings->set_flashdata('success','Usuario creado exitósamente.');
			foreach($_POST as $k => $v){
				if($k != 'id'){
					if(!empty($data)) $data .=" , ";
					$this->settings->set_userdata($k,$v);
				}
			}
					$this->settings->set_userdata('id',$this->conn->insert_id);
					$this->settings->set_userdata('login_type',2);
					if(isset($fname) && isset($move))
						$this->settings->set_userdata('avatar',$fname);
					return 1;
		}else{
			return 2;
		}
	}
	public function save_users(){
		extract($_POST);
		$data = '';
		foreach($_POST as $k => $v){
			if(!in_array($k,array('id','password'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = md5($password);
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}

		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
				$fname = 'uploads/'.strtotime(date('y-m-d H:i')).'_'.$_FILES['img']['name'];
				if(isset($_SESSION['userdata']['avatar']) && is_file('../'.$_SESSION['userdata']['avatar']) && !empty($id))
						unlink('../'.$_SESSION['userdata']['avatar']);
				$move = move_uploaded_file($_FILES['img']['tmp_name'],'../'. $fname);
				if($move){
					$data .=" , avatar = '{$fname}' ";
					
				}
		}
		
		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully saved.');
				// foreach($_POST as $k => $v){
				// 	if($k != 'id'){
				// 		if(!empty($data)) $data .=" , ";
				// 		$this->settings->set_userdata($k,$v);
				// 	}
				// }
				return 1;
			}else{
				return 2;
			}

		}else{
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				foreach($_POST as $k => $v){
					if($k != 'id'){
						if(!empty($data)) $data .=" , ";
						if($id == $this->settings->info('id'))
						$this->settings->set_userdata($k,$v);
					}
				}
						if(isset($fname) && isset($move) && $id == $this->settings->info('id'))
						$this->settings->set_userdata('avatar',$fname);

				return 1;
			}else{
				return "UPDATE users set $data where id = {$id}";
			}
			
		}
	}
	public function delete_user(){
		extract($_POST);
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}

	public function delete_customers(){
		extract($_POST);
		$qry = $this->conn->query("DELETE FROM customers where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'load_users':
		echo $users->load_users();
	break;
	case 'load_customers':
		echo $users->load_customers();
	break;
	case 'save':
		echo $users->save_users();
	break;
	case 'create_customer':
		echo $users->create_customer();
	break;
	case 'delete_user':
		echo $users->delete_user();
	break;
	case 'delete_customers':
		echo $users->delete_customers();
	break;
	default:
		// echo $sysset->index();
		break;
}