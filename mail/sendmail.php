<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
// require 'vendor/autoload.php';
// $mail = new PHPMailer();
	function sendMail($ToEmail, $FromEmail, $FromName, $SubjectSend, $BodySend,$CC="",$BCC="")
	{
		
		if(false)
		{
			require_once ("class.phpmailer.php");
			// $mail = new PHPMailer();
			$mail->Mailer = "mail";
			// $mail->Host = 'smtp.gfg.com;'; // SMTP server
			// $smtp->localhost="localhost";
			$mail->Host       = "smtp.gmail.com";
			$mail->port = 587;
			$mail->encryption = "ssl";
			$mail->AddAddress($ToEmail);
			$mail->Subject = $SubjectSend;
			$mail->From = $FromEmail;
			$mail->FromName = $FromName;
			$mail->Body = $BodySend;
			$mail->ContentType="text/html";
			$mail->WordWrap = 50;
			$mail->AddCC($CC);
			$mail->AddBCC($BCC);
			//$mail->attachment=SITE_DOC ."\\".$attach;

			if(!$mail->Send()) return $mail->ErrorInfo;
			else return "";
		}
		else
		{
			
			require_once ("class.phpmailer.php");
			// $mail = new PHPMailer(true);
			$mail->Mailer = "mail";
			// $mail->Host = 'smtp.gfg.com;'; // SMTP server
			// $smtp->localhost="localhost";
			// $mail->Host       = "smtp.gmail.com";
			$mail->Host       = 'smtp.gfg.com;';
			$mail->Username   = 'kuldeep.ratiocinativesolutions@gmail.com';
			$mail->Password   = 'password';                        
			$mail->SMTPSecure = 'tls';                              
			$mail->Port       = 587;  
		
			$mail->setFrom('kuldeep.ratiocinativesolutions@gmail.com', 'Kuldeep');           
			$mail->addAddress('kuldeep@netzoptimize.com');
			// $mail->addAddress('receiver2@gfg.com', 'Name');
			
			$mail->isHTML(true);                                  
			$mail->Subject = $SubjectSend;
			$mail->Body    = $BodySend;
			// $mail->AltBody = 'Body in plain text for non-HTML mail clients';
			$mail->send();
			echo "Mail has been sent successfully!";
			// $mail->port = 587;
			// $mail->encryption = "ssl";
			// // $mail->AddAddress($ToEmail);
			// $mail->AddAddress("kuldeep@netzoptimize.com");
			// $mail->Subject = $SubjectSend;
			// $mail->From = $FromEmail;
			// $mail->FromName = $FromName;
			// $mail->Body = $BodySend;
			// $mail->ContentType="text/html";
			// $mail->WordWrap = 50;
			// $mail->AddCC($CC);
			// $mail->AddBCC($BCC);
			// //$mail->attachment=SITE_DOC ."\\".$attach;

			// if(!$mail->Send()) return $mail->ErrorInfo;
			// else return "";


			// require_once("smtp.php");

			// require_once("sasl.php");
			// $from="kuldeep.ratiocinativesolutions@gmail.com";
			// $to	=	$ToEmail;
			// $subject	=	$SubjectSend;
			// $message	=	$BodySend;
			// $smtp=new smtp_class;
			// $smtp->port = 587;
			// $smtp->encryption = "ssl";

			// // $smtp->host_name="smtpout.secureserver.net"; 
			// $smtp->host_name       = "smtp.gmail.com";
			// $smtp->host_port=80; 
			// $smtp->ssl=0;    
			// $smtp->start_tls=0;                
			// // $smtp->localhost='smtp.gfg.com;';
			// $smtp->localhost="localhost";
			// $smtp->direct_delivery=0;           
			// $smtp->timeout=10;                  
			// $smtp->data_timeout=0;             
			// $smtp->debug=1;                     
			// $smtp->html_debug=1;               
			// $smtp->pop3_auth_host="";          
			// $smtp->user="kuldeep.ratiocinativesolutions@gmail.com";                   
			// $smtp->realm="";                    
			// // $smtp->password='icue@71RIoU$';
			// $smtp->password='Kuldeep@64';              
			// $smtp->workstation="";              
			// $smtp->authentication_mechanism=""; 
			// if($smtp->SendMessage($from,array($to), array(
			// 		"From: $from",
			// 		"To: $to",
			// 		"Subject: $subject",
			// 		"Content-Type: text/html; charset=ISO-8859-1",
			// 		"MIME-Version: 1.0",
			// 		"Date: ".strftime("%a, %d %b %Y %H:%M:%S %Z")
			// 	),
			// 	"$message"))
			// 	{
			// 		return '';
			// 	}
			// else
			// 	echo "Cound not send the message to $to.\nError: ".$smtp->error."\n";
		}
	}
?>
