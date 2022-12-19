<?php
include_once("private/settings.php");
include_once("classes/Users.php");

$objUser				=	new Users();

$message				=	array('error', 'success');
$objUser->accessTocken	=	$_GET['tocken'];
$resultUser				=	$objUser->selectUser();
$showForm				=	1;

if (!empty($resultUser) && is_array($resultUser)	&&	count($resultUser) == 1) {
	if (isset($_POST['email'])	&&	!empty($_POST['email'])) {
		$rowUser			=	$resultUser[0];
		$recipientEmail		=	trim($rowUser->email);

		if ($recipientEmail	==	trim($_POST['email'])) {
			$objUser->userId = $rowUser->user_id;
			$objUser->email = $recipientEmail;
			$objUser->password = trim($_POST['password']);
			$objUser->accessTocken = ' ';
			$objUser->updateUserPassword();

			$_SESSION['user']				=	array();

			$_SESSION['user']['id']			=	$resultUser[0]->user_id;
			$_SESSION['user']['firstName']	=	$resultUser[0]->first_name;
			$_SESSION['user']['lastName']	=	$resultUser[0]->last_name;
			$_SESSION['user']['email']		=	$resultUser[0]->email;
			$_SESSION['user']['usertype']	=	$resultUser[0]->usertype;

			$allowLogin	=	1;
			$message['success']	=	'<div class="success_msg">You have successfully changed the password, now you are being redirected to your search Page.</div>';

			echo '<script> setInterval(function(){ window.location.assign("' . URL . '"); }, 3000);</script>';
		} else {
			$message['error']	=	'<div class="error_msg">You cannot change the password for this user. Please use the correct username.</div>';
		}
	}
} else {
	$showForm			=	0;
	$message['error']	=	'<div class="error_msg">This URL has expired. If you want to reset the password again, you can follow the forgot password process again.</div>';
}
?>

<html>

<head>
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,900" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="style.css">
	<style>
		body {
			font-family: "Lato", sans-serif;
			margin: 0;
		}

		.clear::after {
			content: "";
			display: table;
			clear: both;
		}
	</style>
	<meta charset="utf-8">
	<link rel="icon" href="images/logo/favicon2.png" type="image/x-icon">
	<title>iCUEVIEW</title>
</head>

<body class="reset-password">
	<div class="header">
		<div class="wrapper1">
			<div class="top-left">
				<div class="logo">
					<a href="<?php echo URL; ?>"><img src="images/home/logo.png"></a>
				</div>
				<div class="search-button">
					<form method="get" action="<?php echo URL; ?>">
						<div class="selectFieldContainerTop" id="selectContainerTop">
							<select name="patent_code" required="">
								<option value="US" selected="">US</option>
								<option value="WO">WO</option>
								<option value="ALL">ALL</option>
							</select>
						</div>
						<div class="inputFieldContainerTop">
							<input id="search-input" name="patent_id" value="20010000045" required="" type="text">
							<img src="images/search-ico.png">
						</div>
						<input name="msearch" value="1" type="hidden">
					</form>
				</div>
				<div class="clr"></div>
			</div>
			<div class="top-left text-right" id="user-options">
				<a class="login-link">Login</a>
				<a class="register-link">Register</a>
				<div class="clr"></div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<div class="clear"></div>
	<div class="wrapper">
		<div class="row inner-page">
			<div class="container">
				<div class="col-lg-8">
					<?php
					if (!empty($message['success'])) {
						echo $message['success'];
					} elseif (!empty($_GET['tocken'])) {
					?>

						<form method="POST" action="#" name="resetPassword" class="form-horizontal resetPassword">
							<div class="col-lg-12">
								<div class="form-heading">
									<h1>Set a New Password For Your Account.</h1>
								</div>
							</div>

							<div class="">
								<label></label>
								<div class="input-container">
									<div class="hide-div" id="login-response"></div>
								</div>
							</div>
							<div class="">
								<label>Email</label>
								<div class="input-container">
									<input type="text" placeholder="Email" name="email" id="email" value="" class="form-control">
									<span class="error"></span>
								</div>
							</div>
							<div class=" ">
								<label>New Password</label>
								<div class="input-container">
									<input type="password" placeholder="Password" name="password" id="password" class="form-control">
									<span class="error"></span>
								</div>
							</div>
							<div class=" ">
								<label>Confirm New Password</label>
								<div class="input-container">
									<input type="password" placeholder="Password" name="confirmPassword" id="confirmPassword" class="form-control">
									<span class="error "></span>
								</div>
							</div>
							<div class="input-container">
								<div class="save-password">
									<button class="btn btn-default" type="submit" id="resetPasswordSubmit">Save Password</button>
									<div class="form-group col-lg-12">
										<?php
										if (!empty($message['error']))
											echo $message['error'];
										?>
									</div>
								</div>
							</div>
						</form>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="bg-overlay"></div>
	<div class="login-form-container">
		<div class="popup-header">
			<div class="close-popup">X</div>
			<div class="popup-title">Login</div>

		</div>
		<form name="login-form" class="login-form">
			<div class="input-container">
				<input name="email" type="text" placeholder="Enter your email" />
				<label class="error hide-div"></label>
			</div>
			<div class="input-container">
				<input name="password" type="password" placeholder="Enter password" />
				<label class="error hide-div"></label>
			</div>
			<button id="login-button">Login</button>
			<a href="#" class="register-now-link">Not Registered? Register Now.</a>
		</form>
		<a href="#" class="forgot-password-link">Forgot Password</a>
		<div class="error-box"></div>
	</div>
	<div class="forgot-pwd-form-container">
		<div class="popup-header">
			<div class="close-popup">X</div>
			<div class="popup-title">Forgot Password</div>
		</div>
		<form name="forgot-pwd-form" class="forgot-pwd-form">
			<div class="input-container">
				<input name="email" type="text" placeholder="Enter your email" />
				<label class="error hide-div"></label>
			</div>
			<button id="forgot-pwd-button">Get Password Link</button>
		</form>
		<a href="#" class="login-now-link">Back to Login.</a>
		<div class="error-box"></div>
	</div>
	<div class="register-form-container">
		<div class="popup-header">
			<div class="close-popup">X</div>
			<div class="popup-title">Register</div>

		</div>
		<form name="register-form" class="register-form">
			<div class="input-container">
				<input name="firstName" type="text" placeholder="Enter first name" />
				<label class="error hide-div"></label>
			</div>
			<div class="input-container">
				<input name="lastName" type="text" placeholder="Enter last name" />
				<label class="error hide-div"></label>
			</div>
			<div class="input-container">
				<input name="email" type="text" placeholder="Enter your email" />
				<label class="error hide-div"></label>
			</div>
			<div class="input-container">
				<input name="password" type="password" placeholder="Enter password" />
				<label class="error hide-div"></label>
			</div>
			<div class="input-container">
				<input name="confirmPassword" type="password" placeholder="Confirm password" />
				<label class="error hide-div"></label>
			</div>
			<button id="register-button">Register</button> <a href="#" class="already-registered-now-link">Already registered? Login Here.</a>
		</form>
		<div class="error-box"></div>
	</div>

	<div class="message-box-container">
		<div class="close-popup">X</div>
		<div class="message-box">
			You are sucessfully logged in. Please wait for a moment.
		</div>
	</div>

	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
	<script src="js/clipboard.min.js"></script>
	<script src="https://swisnl.github.io/jQuery-contextMenu/dist/jquery.contextMenu.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/color-picker.min.js"></script>
	<script src="js/main.js"></script>
</body>

</html>