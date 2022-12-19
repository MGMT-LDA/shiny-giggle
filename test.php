<?php
	error_reporting(E_ALL);

	include_once('private/settings.php');
	include_once('classes/Patents.php');


	$objPatents					=		new Patents();
	
	$patentFamily				=		$objPatents->getPatentFamily('https://worldwide.espacenet.com/publicationDetails/biblio?DB=EPODOC&II=0&ND=3&adjacent=true&locale=en_EP&FT=D&date=19971021&CC=US&NR=5678984A&KC=A');
?>
