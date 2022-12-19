<?php
//https://www.icuerious.com/icueview/download.php?file=/fdd/06/2020/11/002/0.pdf&name=20200021106
//https://www.icuerious.com/icueview/download.php?file=//pimg-fpiw.uspto.gov/fdd/08/879/086/0.pdf&name=8687908
	if(isset($_GET['file']) && !empty($_GET['file']))
	{
		$download_name=$_GET['name'].'.pdf';
		$file_name = basename($_GET['file']);
		$file_url = $_GET['file'];
		if(substr($file_url,0,2)=='//')
		{
		 $file_url = 'https:'.$file_url;	
		}
		else if(substr($file_url,0,4)=='/fdd')
		{
			$file_url = 'https://pdfaiw.uspto.gov/'.$file_url;
			
		}
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary"); 
		header("Content-disposition: attachment; filename=\"".$download_name."\""); 
		readfile($file_url);
		exit;
	}
?>


