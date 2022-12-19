<?php 
	include_once(PATH."classes/Utility.php");
	include_once(PATH."mail/sendmail.php");
	Class EmailNotification
	{
		var $to = '';
		var $from = '';
		var $subject = '';
		var $message = '';
		var $senderName = '';
		var $cc = '';
		
		var $access_tocken = '';
		var $user = '';
		
		function sendEmail()
		{
			$result = sendmail($this->to,$this->from,$this->senderName,$this->subject,$this->message,$this->cc);
			
			$objUtility = new Utility();
			$objUtility->datatableidField ='';
			$objUtility->dataTable = '';
			$objUtility->action='Email sent';
			$objUtility->description='Email sent to "'.$this->to.'" from "'.$this->senderName.'<'.$this->from.'>"';
			$objUtility->logTrack();
			return $result;
		}
		
		function forgotPasswordmail()
		{
			$this->message='Hi '.$this->user.',<br/><br/>We have received a request to help you reset your password. Click on the following link to reset your password. <br/><br/> <a href="'.URL.'reset-password.php?tocken='.$this->access_tocken.'">'.URL.'reset-password.php?tocken='.$this->access_tocken.'</a><br><br>If you did not request for a new password, please ignore this email.<br><br>Thank you <br/> Icuerious';
			sendMail($this->to,'kuldeep@netzoptimize.com','Icuerious',$this->subject,$this->message,$CC1="");
			
		}
		
		function sendPaymentRenewalEmail()
		{
			$this->subject	=	GLOBAL_TITLE.': Payment Renewal';
			sendmail($this->to,ADMIN_EMAIL_FROM,GLOBAL_TITLE,$this->subject,$this->message,$CC1="");
		}
	}
?>