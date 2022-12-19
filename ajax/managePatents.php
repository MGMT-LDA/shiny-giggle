<?php
	include_once('../private/settings.php');
	include_once(PATH.'classes/Patents.php');
	// print_r(($_POST));
	if(isset($_POST['action']))
	{
		$objPatents		=	new Patents();
		if($_POST['action'] == 'save')
		{
			if(!isset($_SESSION['user']) || empty($_SESSION['user']))
			{
				echo json_encode(array('status'=>'error','message'=>'You must login to save the patent changes.'));
			}
			else
			{
				$objPatents->patent_id		=	$_SESSION['patentSearch']['patent_id'];
				$objPatents->patentCode		=	$_SESSION['patentSearch']['patentCode'];
				$objPatents->pdfLink		=	$_SESSION['patentSearch']['pdfLink'];
				$objPatents->espacenetUrl	=	$_SESSION['patentSearch']['espacenetUrl'];
				$objPatents->patentFamily	=	$_SESSION['patentSearch']['patentFamily'];
				$objPatents->userId			=	$_SESSION['user']['id'];
				
				$objPatents->content		=	$_POST['content'];
				$objPatents->claim			=	$_POST['claim'];
				$objPatents->description	=	$_POST['description'];
				$objPatents->claim1			=	$_POST['claim_1'];
				$objPatents->description1	=	$_POST['description_1'];
				
				$result						=	$objPatents->savePatentForUser();
				
				if(!empty($result))
					echo json_encode(array('status'=>'ok','message'=>'Patent Changes saved.'));
				else
					echo json_encode(array('status'=>'error','message'=>'Unable to save the changes.'));
			}
		}
	}
?>