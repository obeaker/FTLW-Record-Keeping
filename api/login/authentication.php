<?php

  require_once('../../../config.php');
  
  $app->get('/session', function() {
      $db = new DbHandler();
      $session = $db->getSession();
      $response["uid"] = $session['uid'];
      $response["email"] = $session['email'];
      $response["name"] = $session['name'];
      $response["role"] = $session['role'];
      $response["loggedin_time"] = $session['loggedin_time'];
      $response["current_time"] = $session["current_time"];
      echoResponse(200, $session);
  });

  $app->post('/login', function() use ($app) {
      require_once 'passwordHash.php';
      $r = json_decode($app->request->getBody());
      verifyRequiredParams(array('username', 'password'),$r->user);
      $response = array();
      $db = new DbHandler();
      $password = $r->user->password;
      $username = $r->user->username;
      $ip = $_SERVER['REMOTE_ADDR'];

      $result = $db->confirmIPAddress($ip, $username);

      if($result == 1){
        $response['status'] = "error";
        $response['message'] = "Access denied for ".TIME_PERIOD." minutes";
        echoResponse(200, $response);
        return;
      }

      $user = $db->getOneRecord("select uid,name,username,password,email,approved,role,locked from ftlw_users where username='$username' and approved='1'");
      if ($user != NULL) {
        if ((($ip == RESTRICTED_IP) && ($user['role'] == "User")) || ($user['role'] == "Admin") ) {
            if(passwordHash::check_password($user['password'],$password)){
              $response['status'] = "success";
              $response['message'] = 'Logged in successfully.';
              $response['name'] = $user['name'];
              $response['uid'] = $user['uid'];
              $response['email'] = $user['email'];
              $response['role'] = $user['role'];
              $db->clearLoginAttempts($ip, $username);
              if (!isset($_SESSION)) {
                  session_start();
              }
              $_SESSION['uid'] = $user['uid'];
              $_SESSION['email'] = $user['email'];
              $_SESSION['name'] = $user['name'];
              $_SESSION['role'] = $user['role'];
              $_SESSION['loggedin_time'] = time();
            }
            else {
              if($user['locked'] === 1) {
                $subject = "Access denied for ".TIME_PERIOD." minutes";
                $message = 'Login failed. You have entered the incorrect credentials multiple times and your account is now locked' ;
                $db->sendEmail($subject, $message);
              }
              else {
                $subject = 'Record Keeping Login failed. Incorrect credentials';
                $message = 'Login failed. Incorrect credentials' ;
                //sendEmail($subject, $message);
                $db->addLoginAttempt($ip, $username);
                $response['status'] = "error";
                $response['message'] = 'Login failed. Incorrect credentials';
              }
            }
        }
        else {
          $subject = 'Record Keeping Login Attempt Outside Church';

          $message = 'Login Attempt Outside Church' . $ip ;
          $db->sendEmail($subject, $message);

          $response['status'] = "error";
          $response['message'] = 'You are not allowed to login at this time';
        }
      }
      else {
        $subject = 'Record Keeping Login Attempt User Not Found or Not Approved';
        $message = 'Login Attempt User Not Found/ Not Approved';
        $db->sendEmail($subject, $message);

        $response['status'] = "error";
        $response['message'] = 'No such user is registered or user is not yet approved';
      }
      echoResponse(200, $response);
  });

  $app->post('/register', function() use ($app) {
      $response = array();
      $r = json_decode($app->request->getBody());
      verifyRequiredParams(array('email', 'name', 'password', 'username'),$r->user);
      require_once 'passwordHash.php';
      $db = new DbHandler();
      $username = $r->user->username;
      $name = $r->user->name;
      $email = $r->user->email;
      $password = $r->user->password;
      $isUserExists = $db->getOneRecord("select 1 from ftlw_users where username='$username' or email='$email'");
      if(!$isUserExists){
          $r->user->password = passwordHash::hash($password);
          $table_name = "ftlw_users";
          $column_names = array('username', 'name', 'email', 'password');
          $result = $db->insertIntoTable($r->user, $column_names, $table_name);
          if ($result != NULL) {
              $response["status"] = "success";
              $response["message"] = "User account created successfully";
              $response["uid"] = $result;
              if (!isset($_SESSION)) {
                  session_start();
              }
              $_SESSION['uid'] = $response["uid"];
              $_SESSION['username'] = $username;
              $_SESSION['name'] = $name;
              $_SESSION['email'] = $email;


              $subject = 'User Account Created Successfully';
              $message = 'Your account has been created successfully and is awaiting approval from the administration team.' ;
              $db->sendEmail($subject, $message);

              echoResponse(200, $response);
          } else {
              $response["status"] = "error";
              $response["message"] = "Failed to create user. Please try again";
              echoResponse(201, $response);
          }
      }else{
          $response["status"] = "error";
          $response["message"] = "A user with the provided username or email exists!";
          echoResponse(201, $response);
      }
  });

  $app->get('/logout', function() {
      $db = new DbHandler();
      $session = $db->destroySession();
      $response["status"] = "info";
      $response["message"] = "Logged out successfully";
      echoResponse(200, $response);
  });

?>
