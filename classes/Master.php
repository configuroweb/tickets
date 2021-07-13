<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function load_service_category(){
		
		$qry = $this->conn->query("SELECT * FROM `services_category` order by `category` asc");
		$data = array();
			while($row = $qry->fetch_assoc()){
				$row['description'] = strip_tags(stripslashes($row['description']));
				
				$data[] = $row;
			}	
			$resp['status']='success';
			$resp['data'] = $data;
			return json_encode($resp);
	}
	public function load_service(){
		
		$qry = $this->conn->query("SELECT s.*,c.category FROM `services` s inner join `services_category` c on c.id = s.category_id order by s.`service` asc");
		$data = array();
			while($row = $qry->fetch_assoc()){
				$row['description'] = strip_tags(stripslashes($row['description']));
				$row['img_path'] = validate_image($row['img_path']);
				$data[] = $row;
			}	
			$resp['status']='success';
			$resp['data'] = $data;
			return json_encode($resp);
	}
	public function load_tickets(){
		extract($_POST);
		$qry = $this->conn->query("SELECT t.*,s.service,c.category FROM `tickets` t inner join `services` s on t.service_id = s.id inner join `services_category` c on c.id = s.category_id ".($this->settings->userdata('login_type') != 1 ? " where t.user_id = '".$this->settings->userdata('id')."' and t.user_created ='user' " : "")." order by date(date_created) desc");
		$data = array();
			while($row = $qry->fetch_assoc()){
				$row['description'] = strip_tags(stripslashes($row['description']));
				if($row['user_id'] > 0){
					if($row['user_created'] == 'admin'){
						$user= $this->conn->query("SELECT * FROM users where id =".$row['user_id']);
						if($user->num_rows > 0)
							$row['user_avatar'] = validate_image($user->fetch_array()['avatar']);
					}else{
						$user= $this->conn->query("SELECT * FROM customers where id =".$row['user_id']);
						if($user->num_rows > 0)
							$row['user_avatar'] = validate_image($user->fetch_array()['avatar']);
					}
				}
				$row['date_created'] = date("Y-m-d h:i A",strtotime($row['date_created']));
				switch ($row['status']) {
					case 1:
						$row['status'] ='On-Going';
						$row['status_badge'] ='badge-info';
						break;
					case 2:
						$row['status'] ='Closed';
						$row['status_badge'] ='badge-success';
						break;
					default:
						$row['status'] ='Pending';
						$row['status_badge'] ='badge-dark';
						break;
				}
				if(empty($search))
				$data[] = $row;
				elseif(strstr(strtolower($row['title']),strtolower($search)) || strstr(strtolower($row['description']),strtolower($search)))
				$data[] = $row;
				 
			}	

			$resp['status']='success';
			$resp['data'] = $data;
			return json_encode($resp);

	}
	public function load_comments(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM `ticket_comment` where ticket_id = {$id} order by date(date_created) desc");
		$data = array();
			while($row = $qry->fetch_assoc()){
				$row['comment'] = strip_tags(stripslashes($row['comment']));
				$row['user_name'] = "Developer";
				$row['login_type'] = 1;
				if($row['user_id'] > 0){
					if($row['user_created'] == 'admin'){
						$user= $this->conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id =".$row['user_id']);
						if($user->num_rows > 0){
							$res =$user->fetch_array();
							$row['user_avatar'] = validate_image($res['avatar']);
							$row['user_name'] = ucwords($res['name']);
						}
							
					}else{
						$user= $this->conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM customers where id =".$row['user_id']);
						if($user->num_rows > 0){
							$res =$user->fetch_array();
							$row['user_avatar'] = validate_image($res['avatar']);
							$row['user_name'] = ucwords($res['name']);
							$row['login_type'] = 2;
						}
				}
				}
				$row['date_created'] = date("Y-m-d h:i A",strtotime($row['date_created']));
				$data[] = $row;
			}	

			$resp['status']='success';
			$resp['data'] = $data;
			return json_encode($resp);

	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if($k == 'description') $v = addslashes($v);
				if(!empty($data)) $data .= " , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		$chk = $this->conn->query("SELECT * FROM `services_category` where category = '{$category}' ".(!empty($id) ? " and id != {$id}" : ""));
		if($chk->num_rows > 0){
			$resp['status'] = 'duplicate';
		}
		if(empty($id)){
			$sql = "INSERT INTO `services_category` set $data ";
		}else{
			$sql = "UPDATE `services_category` set $data where id = {$id}";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
		}else{
			$resp['error'] = 'error';
			$resp['data'] = $sql;
		}
		return json_encode($resp);

	}
	function delete_service_category(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `services_category` where id ='$id' ");
		$delete2 = $this->conn->query("DELETE FROM `services` where category_id ='$id' ");
		if($delete && $delete2){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_service(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if($k == 'description') $v = addslashes($v);
				if(!empty($data)) $data .= " , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		$chk = $this->conn->query("SELECT * FROM `services` where service = '{$service}' ".(!empty($id) ? " and id != {$id}" : "")) or die($this->conn->error);
		if($chk->num_rows > 0){
			$resp['status'] = 'duplicate';
		}

		if(empty($id)){
			$sql = "INSERT INTO `services` set $data ";
		}else{
			$sql = "UPDATE `services` set $data where id = {$id}";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$id= !empty($id) ? $id : $this->conn->insert_id;
			if(!is_dir(base_app.'uploads/services')) mkdir(base_app.'uploads/services');
			if(!empty($_FILES['img']['tmp_name'])){
				$file = pathinfo($_FILES["img"]["name"]);
				$fname = $id.'_img.'.($file['extension']);
				if(is_file(base_app.'uploads/services/'.$fname)){
					unlink(base_app.'uploads/services/'.$fname);
				}
				$move = move_uploaded_file($_FILES["img"]["tmp_name"], base_app.'uploads/services/'.$fname);
				if($move){
					$data = " img_path = 'uploads/services/{$fname}' ";
					$this->conn->query("UPDATE `services` set {$data} where id = $id ");
				}else{
					var_dump($move);exit;
				}
			}
		}else{
			$resp['error'] = 'error';
			$resp['data'] = $sql;
		}
		return json_encode($resp);

	}
	function delete_service(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `services` where `id` ='$id' ");
		if($delete){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function delete_ticket(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `tickets` where `id` ='$id' ");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',' Ticket Succesfully deleted');
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_ticket(){
		extract($_POST);
		$data = "";

		foreach($_POST as $k => $v){
			if(!in_array($k, array('id'))){
				if($k == 'description') $v = addslashes($v);
				if(!empty($data)) $data .= " , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		$data .= ", user_id = '".$this->settings->userdata('id')."' ";

		if($this->settings->userdata('id') == -1){
				$data .= ", user_created = 'developer' ";
		}else if($this->settings->userdata('login_type') == 1 ){
				$data .= ", user_created = 'admin' ";
		}else{
				$data .= ", user_created = 'user' ";
		}

		if(empty($id)){
			$sql = "INSERT INTO `tickets` set $data ";
		}else{
			$sql = "UPDATE `tickets` set $data where id = {$id}";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',' Ticket successfully saved');
		}else{
			$resp['error'] = 'error';
			$resp['data'] = $sql;
		}
		return json_encode($resp);
	}
	function save_comment(){
		extract($_POST);
		$comment = addslashes($comment);

		$data = " comment = '{$comment}'";
		$data .= ", user_id = '".$this->settings->userdata('id')."' ";

		if($this->settings->userdata('id') == -1){
				$data .= ", user_created = 'developer' ";
		}else if($this->settings->userdata('login_type') == 1 ){
				$data .= ", user_created = 'admin' ";
		}else{
				$data .= ", user_created = 'user' ";
		}
		$data .= ", ticket_id = '{$ticket_id}' ";

			$sql = "INSERT INTO `ticket_comment` set $data ";
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',' Comment Succesfully posted');
		}else{
			$resp['error'] = 'error';
			$resp['data'] = $sql;
		}
		return json_encode($resp);
	}
	function delete_comment(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `ticket_comment` where `id` ='$id' ");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',' Comment Succesfully deleted');
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function save_quote(){
		extract($_POST);
		$data = " email = '$email' ";
		$service_ids = implode(',',$service);
		$data .= " , service_ids = '{$service_ids}' ";
		$sql = "INSERT INTO `quote` set $data ";
		if($this->conn->query($sql)){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',' Request sent');
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error .' : '.$sql;
		}
		return json_encode($resp);
	}
	function load_quote(){
		$qry = $this->conn->query("SELECT * from `quote` order by date(date_created) desc");
		$data =array();
		if($qry){
			while($row = $qry->fetch_assoc()){
				$services='';
				$qry2 = $this->conn->query("SELECT * from `services` where id in ({$row['service_ids']}) ");
				while($ser = $qry2->fetch_assoc()){
					if(!empty($services)) $services .= ", ";
					$services .= $ser['service'];
				}
				$row['services'] = $services;
				$row['date_created'] = date('Y-m-d h:i A',strtotime($row['date_created']));
				$data[] = $row;
			}
			$resp['status'] = 'success';
			$resp['data'] = $data;

		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error .' : '.$sql;
		}
		return json_encode($resp);
	}
	function delete_quote(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `quote` where `id` ='$id' ");
		if($delete){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'error';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function ticket_update_status(){
		extract($_POST);
		$sql = "UPDATE `tickets` set status = {$status} where id = {$id}";
		if($this->conn->query($sql)){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',' Ticket\'s successfully updated.');
		}else{
			$resp['status'] = 'error';
			$resp['sql'] = $sql;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'load_service_category':
		echo $Master->load_service_category();
	break;
	case 'load_service':
		echo $Master->load_service();
	break;
	case 'load_tickets':
		echo $Master->load_tickets();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_service_category':
		echo $Master->delete_service_category();
	break;
	case 'save_service':
		echo $Master->save_service();
	break;
	case 'delete_service':
		echo $Master->delete_service();
	break;
	case 'save_ticket':
		echo $Master->save_ticket();
	break;
	case 'save_comment':
		echo $Master->save_comment();
	break;
	case 'load_comments':
		echo $Master->load_comments();
	break;
	case 'delete_comment':
		echo $Master->delete_comment();
	break;
	case 'delete_ticket':
		echo $Master->delete_ticket();
	break;
	case 'save_quote':
		echo $Master->save_quote();
	break;
	case 'load_quote':
		echo $Master->load_quote();
	break;
	case 'delete_quote':
		echo $Master->delete_quote();
	break;
	case 'ticket_update_status':
		echo $Master->ticket_update_status();
	break;
	default:
	// echo $sysset->index();
	break;
}