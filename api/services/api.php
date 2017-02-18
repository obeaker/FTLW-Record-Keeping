<?php
 	require_once("Rest.inc.php");
  include_once '../config.php';
	class API extends REST {

		public $data = "";

		private $db = NULL;
		private $mysqli = NULL;
		public function __construct(){
			parent::__construct();				// Init parent contructor
			$this->dbConnect();					// Initiate Database connection
		}

		/*
		 *  Connect to Database
		*/
		private function dbConnect(){
			$this->mysqli = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
		}

		/*
		 * Dynmically call the method based on the query string
		 */
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['x'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404); // If the method not exist with in this class "Page not found".
		}

		private function records(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.recordNumber, c.recordName, c.cash,
        c.less_expense, c.new_cash, c.checks, c.total, c.recordDate,
        c.hundredsCt, c.fiftysCt, c.twentysCt, c.tensCt, c.fivesCt, c.onesCt,
        c.coinsCt  FROM ftlw_recordkeepings c order by c.recordNumber desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}

		private function givingfund(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.fund_name  FROM ftlw_givingfund c order by c.id desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}

		private function attendances(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.attendanceNumber, c.adults, c.children,
        c.total, c.user, c.attendanceDate FROM ftlw_recordattendance c order by
        c.attendanceNumber desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}

    private function allrecords(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.recordNumber, c.recordName, c.cash,
        c.less_expense, c.new_cash, c.checks, c.total, c.recordDate,
        c.hundredsCt, c.fiftysCt, c.twentysCt, c.tensCt, c.fivesCt, c.onesCt,
        c.coinsCt  FROM ftlw_recordkeepings c order by c.recordNumber desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}

    private function allusers(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$query="SELECT distinct c.uid, c.name, c.email, c.username,
        c.approved, c.role, c.locked  FROM ftlw_users c order by c.uid desc";
			$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);

			if($r->num_rows > 0){
				$result = array();
				while($row = $r->fetch_assoc()){
					$result[] = $row;
				}
				$this->response($this->json($result), 200); // send user details
			}
			$this->response('',204);	// If no records "No Content" status
		}

    private function record(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){
				$query="SELECT distinct c.recordNumber, c.recordName, c.cash,
          c.less_expense, c.new_cash, c.checks, c.total, c.hundredsCt,
          c.fiftysCt, c.twentysCt, c.tensCt, c.fivesCt, c.onesCt, c.coinsCt
          FROM ftlw_recordkeepings c where c.recordNumber=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}

    private function status(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$date = $this->_request['date'];
			//if($date !== ""){
				$query="SELECT distinct c.status
          FROM ftlw_recorddate c where c.recordDate='$date'";

				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();
					$this->response($this->json($result), 200); // send user details
				}
			//}
			$this->response('',204);	// If no records "No Content" status
		}

    private function attendance(){
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){
				$query="SELECT distinct c.attendanceNumber, c.adults, c.children,
          c.total, c.user FROM ftlw_recordattendance c
          where c.attendanceNumber=$id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				if($r->num_rows > 0) {
					$result = $r->fetch_assoc();
					$this->response($this->json($result), 200); // send user details
				}
			}
			$this->response('',204);	// If no records "No Content" status
		}

    private function insertRecord(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$record = json_decode(file_get_contents("php://input"),true);
			$column_names = array('recordName', 'cash', 'new_cash', 'less_expense', 'checks', 'total', 'recordDate', 'hundredsCt', 'fiftysCt', 'twentysCt', 'tensCt', 'fivesCt', 'onesCt', 'coinsCt');
			$keys = array_keys($record);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $record[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO ftlw_recordkeepings(".trim($columns,',').") VALUES(".trim($values,',').")";

			if(!empty($record)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Record Created Successfully.", "data" => $record);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}

    private function updateRecord(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$record = json_decode(file_get_contents("php://input"),true);
			$id = (int)$record['id'];
			$column_names = array('recordName', 'cash', 'new_cash', 'less_expense', 'checks', 'total', 'recordDate', 'hundredsCt', 'fiftysCt', 'twentysCt', 'tensCt', 'fivesCt', 'onesCt', 'coinsCt');
			$keys = array_keys($record['record']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $record['record'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE ftlw_recordkeepings SET ".trim($columns,',')." WHERE recordNumber=$id";
			if(!empty($record)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Record ".$id." Updated Successfully.", "data" => $record);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

    private function insertAttendance(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$attendance = json_decode(file_get_contents("php://input"),true);
			$column_names = array('adults', 'children', 'total', 'user', 'attendanceDate');
			$keys = array_keys($attendance);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If blank insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $attendance[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "INSERT INTO ftlw_recordattendance(".trim($columns,',').") VALUES(".trim($values,',').")";
      //error_log($query);
			if(!empty($attendance)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Attendance Created Successfully.", "data" => $attendance);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}

    private function updateAttendance(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$attendance = json_decode(file_get_contents("php://input"),true);
			$id = (int)$attendance['id'];
			$column_names = array('adults', 'children', 'total', 'user', 'attendanceDate');
			$keys = array_keys($attendance['attendance']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $attendance['attendance'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE ftlw_recordattendance SET ".trim($columns,',')." WHERE attendanceNumber=$id";
			if(!empty($attendance)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Attendance ".$id." Updated Successfully.", "data" => $attendance);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

    private function insertDate(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}

			$record = json_decode(file_get_contents("php://input"),true);
			$column_names = array('recordDate', 'status');
			$keys = array_keys($record);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $record[$desired_key];
				}
				$columns = $columns.$desired_key.',';
				$values = $values."'".$$desired_key."',";
			}
			$query = "REPLACE INTO ftlw_recorddate(".trim($columns,',').") VALUES(".trim($values,',').")";

			if(!empty($record)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Record Created Successfully.", "data" => $record);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	//"No Content" status
		}

    private function updateDate(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$attendance = json_decode(file_get_contents("php://input"),true);
			$id = $attendance['date'];
      //error_log("id - "+ $id);
			$column_names = array('status');
			$keys = array_keys($attendance['daterecord']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $attendance['daterecord'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
      //error_log("columns - " + $columns);
			$query = "UPDATE ftlw_recorddate SET ".trim($columns,',')." WHERE recordDate='$id'";
      //error_log($query);
			if(!empty($attendance)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Attendance ".$id." Updated Successfully.", "data" => $attendance);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

    private function updateRole(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$attendance = json_decode(file_get_contents("php://input"),true);
			$id = (int)$attendance['id'];
			$column_names = array('user');
			$keys = array_keys($attendance['attendance']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $attendance['attendance'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE ftlw_recordattendance SET ".trim($columns,',')." WHERE attendanceNumber=$id";
			if(!empty($attendance)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Attendance ".$id." Updated Successfully.", "data" => $attendance);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

    private function updateUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$user = json_decode(file_get_contents("php://input"),true);
			$id = (int)$user['id'];
			$column_names = array('name','email','username','approved','role','locked');
			$keys = array_keys($user['user']);
			$columns = '';
			$values = '';
			foreach($column_names as $desired_key){ // Check the customer received. If key does not exist, insert blank into the array.
			   if(!in_array($desired_key, $keys)) {
			   		$$desired_key = '';
				}else{
					$$desired_key = $user['user'][$desired_key];
				}
				$columns = $columns.$desired_key."='".$$desired_key."',";
			}
			$query = "UPDATE ftlw_users SET ".trim($columns,',')." WHERE uid=$id";
      //error_log($query);
			if(!empty($user)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "User ID ".$id." Updated Successfully.", "data" => $user);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

    private function unlockUser(){
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			$user = json_decode(file_get_contents("php://input"),true);
			$id = (int)$user['id'];
			$username = $user['username'];
      $ip = $_SERVER['REMOTE_ADDR'];

			$query = "UPDATE ftlw_users SET locked = 0 WHERE uid=$id";
      $query1 = "UPDATE ".TBL_ATTEMPTS." SET attempts = 0 WHERE ip = '$ip' and username = '$username'";
      //error_log($query);
			if(!empty($user)){
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
        $r1 = $this->mysqli->query($query1) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "User ID ".$id." Updated Successfully.", "data" => $user);
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// "No Content" status
		}

		private function deleteRecord(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){
				$query="DELETE FROM ftlw_recordkeepings WHERE recordNumber = $id";
				$r = $this->mysqli->query($query) or die($this->mysqli->error.__LINE__);
				$success = array('status' => "Success", "msg" => "Successfully deleted one record.");
				$this->response($this->json($success),200);
			}else
				$this->response('',204);	// If no records "No Content" status
		}

		/*
		 *	Encode array into JSON
		*/
		private function json($data){
			if(is_array($data)){
				return json_encode($data);
			}
		}
	}

	// Initiiate Library

	$api = new API;
	$api->processApi();
?>
