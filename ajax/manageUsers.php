<?php
include_once('../private/settings.php');
include_once('../classes/Users.php');
// print_r($_POST);
// if(isset($_POST['action']) && $_POST['action'] == "login") {
// 	echo "uiooo";
// }

if (isset($_POST['action'])) {
	$objUser	=	new Users();
	if ($_POST['action'] == 'register') {
		$objUser->email							=	$_POST['email'];
		$objUser->isActive						=	1;

		$result									=	$objUser->selectUser();
		if (!is_array($result) || empty($result)) {
			$objUser->firstName					=	$_POST['firstName'];
			$objUser->lastName					=	$_POST['lastName'];
			$objUser->password					=	$_POST['password'];
			$objUser->usertype					=	UserType::CLIENT;
			$result								=	$objUser->insertUser();

			if (!empty($result)) {
				$_SESSION['user']				=	array();

				$_SESSION['user']['id']			=	$result;
				$_SESSION['user']['firstName']	=	$objUser->firstName;
				$_SESSION['user']['lastName']	=	$objUser->lastName;
				$_SESSION['user']['email']		=	$objUser->email;
				$_SESSION['user']['usertype']	=	$objUser->usertype;

				echo json_encode(array('status' => 'ok', 'message' => 'Your have successfully registered. You are logged in to the system. Please wait for a while.'));
			}
		} else
			echo json_encode(array('status' => 'error', 'message' => 'This email is already registered.'));
	} elseif ($_POST['action'] == 'login') {
		// echo "here";
		// print_r($_POST);
		// die();
		$objUser->email						=	$_POST['email'];
		$objUser->password					=	$_POST['password'];
		$objUser->isActive					=	1;
		$result								=	$objUser->selectUser();
		if (empty($objUser->email) || empty($objUser->password)) {
			echo json_encode(array('status' => 'error', 'message' => 'Login failed.'));
		} elseif (is_array($result) && !empty($result)) {
			$_SESSION['user']				=	array();

			$_SESSION['user']['id']			=	$result[0]->user_id;
			$_SESSION['user']['firstName']	=	$result[0]->first_name;
			$_SESSION['user']['lastName']	=	$result[0]->last_name;
			$_SESSION['user']['email']		=	$result[0]->email;
			$_SESSION['user']['usertype']	=	$result[0]->usertype;

			echo json_encode(array('status' => 'ok', 'message' => 'You have successfully logged in to the system. Please wait for a second.'));
		} else
			echo json_encode(array('status' => 'error', 'message' => 'Please enter correct username and password.'));
	} elseif ($_POST['action'] == 'logout') {
		if (isset($_SESSION['user']))
			unset($_SESSION['user']);

		if (!isset($_SESSION['user']))
			echo json_encode(array('status' => 'ok', 'message' => 'You have successfully logged in to the system. Please wait for a second.'));
	} elseif ($_POST['action'] == 'forgotPassword') {
		// the message



		$objUser->email						=	$_POST['email'];
		$objUser->isActive					=	1;
		$result								=	$objUser->selectUser();
		if (empty($objUser->email)) {
			echo json_encode(array('status' => 'error', 'message' => 'Please enter your email.'));
		} elseif (is_array($result) && !empty($result)) {
			$userRow = $result[0];
			$length = 12;
			$accessTocken = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyz"), 0, $length);
			$objUser->userId = $userRow->user_id;
			$objUser->email = $userRow->email;
			$objUser->accessTocken = $accessTocken;
			$objUser->addUserTocken();
			$email = $_POST['email'];
			$subject = "Forgot Password Mail";
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset=iso-8859-1';
			$headers[] = 'From: Icuerious <info@icuerious.com>';
			$msg = "Hi \t" . $email . ",\n\n We have received a request to help you reset your password. Click on the following link to reset your password. \n\n" . '<html><body><br><br> <a href="' . URL . 'reset-password.php?tocken=' . $accessTocken . '">' . URL . 'reset-password.php?tocken=forgot_password</a><br><br></body></html>' . "\n\nIf you did not request for a new password, please ignore this email.\n\nThank you \n Icuerious";

			// use wordwrap() if lines are longer than 70 characters
			$msg = wordwrap($msg, 70);

			// send email
			mail($email, $subject, $msg, implode("\r\n", $headers));

			// 		include_once(PATH."classes/EmailNotification.php");
			// 		$objNotification = new EmailNotification();


			// 		$objNotification->user = $userRow->first_name.' '.$userRow->last_name;

			// 		$objNotification->to = $userRow->email;
			// 		$objNotification->subject = 'Password Reset';
			// 		$objNotification->access_tocken = $accessTocken;
			// 		$objNotification->forgotPasswordmail();

			echo json_encode(array('status' => 'ok', 'message' => 'We have sent an email to you with a link to reset your password.'));
		} else
			echo json_encode(array('status' => 'error', 'message' => 'This email is not registered with our system.'));
	}
}
