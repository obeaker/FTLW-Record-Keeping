<?php

class DbHandler {

    private $conn;

    function __construct() {
        require_once 'dbConnect.php';
        // opening db connection
        $db = new dbConnect();
        $this->conn = $db->connect();
    }
    /**
     * Fetching single record
     */
    public function getOneRecord($query) {
        $r = $this->conn->query($query.' LIMIT 1') or die($this->conn->error.__LINE__);
        return $result = $r->fetch_assoc();
    }

    /**
     * Creating new record
     */
    public function insertIntoTable($obj, $column_names, $table_name) {
        $c = (array) $obj;
        $keys = array_keys($c);
        $columns = '';
        $values = '';
        foreach($column_names as $desired_key){ // Check the obj received. If blank insert blank into the array.
           if(!in_array($desired_key, $keys)) {
                $$desired_key = '';
            }else{
                $$desired_key = $c[$desired_key];
            }
            $columns = $columns.$desired_key.',';
            $values = $values."'".$$desired_key."',";
        }
        $query = "INSERT INTO ".$table_name."(".trim($columns,',').") VALUES(".trim($values,',').")";
        $r = $this->conn->query($query) or die($this->conn->error.__LINE__);

        if ($r) {
            $new_row_id = $this->conn->insert_id;
            return $new_row_id;
            } else {
            return NULL;
        }
    }

    public function getSession(){
      $login_session_duration = 3600;
      if (!isset($_SESSION)) {
          session_start();
      }
      $sess = array();
      if(isset($_SESSION['uid']))
      {
        if(!((time() - $_SESSION['loggedin_time']) > $login_session_duration)) {
          $sess["uid"] = $_SESSION['uid'];
          $sess["name"] = $_SESSION['name'];
          $sess["email"] = $_SESSION['email'];
          $sess["role"] = $_SESSION['role'];
          $sess["loggedin_time"] = $_SESSION['loggedin_time'];
          $sess["current_time"] = time();
      	}
        else {
          $sess["loggedin_time"] = $_SESSION['loggedin_time'];
          $sess["current_time"] = time();
          $sess["uid"] = '';
          $sess["name"] = 'Guest';
          $sess["email"] = '';
          $sess["role"] = '';
      	}
      }
      else
      {
          $sess["uid"] = '';
          $sess["name"] = 'Guest';
          $sess["email"] = '';
          $sess["role"] = '';
          if(isset($_SESSION['loggedin_time'])) {
            $sess["loggedin_time"] = $_SESSION['loggedin_time'];
          }
          else {
            $sess["loggedin_time"] = time();
          }

          $sess["current_time"] = time();
      }
      return $sess;
    }

    public function destroySession(){
      if (!isset($_SESSION)) {
        session_start();
      }
      if(isSet($_SESSION['uid']))
      {
          unset($_SESSION['uid']);
          unset($_SESSION['name']);
          unset($_SESSION['email']);
          unset($_SESSION['role']);
          $info='info';
          if(isSet($_COOKIE[$info]))
          {
              setcookie ($info, '', time() - $cookie_time);
          }
          $msg="Logged Out Successfully...";
      }
      else
      {
          $msg = "Not logged in...";
      }
      return $msg;
    }

    public function isLoginSessionExpired() {
    	$login_session_duration = 1;
    	$current_time = time();
    	if(isset($_SESSION['loggedin_time']) and isset($_SESSION["uid"])){
    		if(((time() - $_SESSION['loggedin_time']) > $login_session_duration)){
    			return true;
    		}
    	}
    	return false;
    }

    public function sendEmail($subject, $message) {
      $mail=new PHPMailer();
      //$mail = new PHPMailerOAuth;
      $mail->CharSet = 'iso-8859-1';

      $mail->SMTPDebug = 0;
      //Ask for HTML-friendly debug output
      $mail->Debugoutput = 'html';

      $mail->IsSMTP();
      $mail->Host       = 'smtp.gmail.com';

      $mail->SMTPSecure = 'tls';
      $mail->Port       = 587;

      $mail->SMTPAuth   = true;

      $mail->Username   = EMAIL_USERNAME;
      $mail->Password   = EMAIL_PASSWORD;

      $mail->SetFrom(FROM_EMAIL, FROM_NAME);
      $mail->AddReplyTo(REPLY_TO_EMAIL,REPLY_TO_NAME);

      $mail->isHTML(true);
      $mail->AddAddress(TO_EMAIL, TO_NAME);
      $mail->Subject    = $subject;

      $mail->Body    = $message;

      if(!$mail->send())
      {
          error_log("Mailer Error: " . $mail->ErrorInfo);
      }
      else
      {
          error_log("Your mail has been sent successfully.");
      }
    }

    public function clearLoginAttempts($value, $username) {
        $q = "UPDATE ".TBL_ATTEMPTS." SET attempts = 0 WHERE ip = '$value' and username = '$username'";
        $result = $this->conn->query($q);
        $locked = 0;
        $q1 = "UPDATE ".TBL_USERS." SET locked=".$locked."  WHERE username = '$username'";
        $result1 = $this->conn->query($q1);
        return;
    }

    public function confirmIPAddress($value, $username) {
    	$q = "SELECT attempts, (CASE when lastlogin is not NULL and DATE_ADD(LastLogin, INTERVAL ".TIME_PERIOD." MINUTE)>NOW() then 1 else 0 end) as Denied ".
       " FROM ".TBL_ATTEMPTS." WHERE ip = '$value' and username = '$username'";

       $result = $this->conn->query($q);
       $data = mysqli_fetch_array($result);
       //error_log($data["attempts"]);
       //Verify that at least one login attempt is in database

       if (!$data) {
         return 0;
       }
       if ($data["attempts"] >= ATTEMPTS_NUMBER)
       {
          if($data["Denied"] == 1)
          {
             return 1;
          }
         else
         {
            $this->clearLoginAttempts($value, $username);
            return 0;
         }
       }
       return 0;
    }

    public function addLoginAttempt($value, $username) {
     // increase number of attempts
     // set last login attempt time if required
  	  $q = "SELECT * FROM ".TBL_ATTEMPTS." WHERE ip = '$value' and username = '$username'";
  	  $result = $this->conn->query($q);
  	  $data = mysqli_fetch_array($result); //$result->fetch_assoc(); // mysql_fetch_array($result);


  	  if($data)
        {
          $attempts = $data["attempts"]+1;
          $locked = 1;

          if($attempts == 3) {
      		 $q = "UPDATE ".TBL_ATTEMPTS." SET attempts=".$attempts.", lastlogin=NOW() WHERE ip = '$value' and username = '$username'";
      		 $result = $this->conn->query($q);

           $q1 = "UPDATE ".TBL_USERS." SET locked=".$locked."  WHERE username = '$username'";
      		 $result1 = $this->conn->query($q1);

           $subject = "Access denied for ".TIME_PERIOD." minutes";
           $message = 'Login failed. You have entered the incorrect credentials multiple times and your account is now locked' ;
           sendEmail($subject, $message);
      		}
          else {
      		 $q = "UPDATE ".TBL_ATTEMPTS." SET attempts=".$attempts." WHERE ip = '$value' and username = '$username'";
      		 $result = $this->conn->query($q);
      		}
        }
        else {
    	   $q = "INSERT INTO ".TBL_ATTEMPTS." (attempts,IP,lastlogin,username) values (1, '$value', NOW(), '$username')";
    	   $result = $this->conn->query($q);
    	  }
    }


}

?>
