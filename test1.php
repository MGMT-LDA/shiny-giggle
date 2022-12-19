<?php
echo '<pre>';
	print_r($_SERVER);
	echo '</pre>';
	// echo $url = 'https://patentscope.wipo.int/search/en/detail.jsf?docId=WO2003008931&recNum=1&maxRec=&office=&prevFilter=&sortOption=&queryString=&tab=PCT+Biblio';
	// echo file_get_contents($url);
	
	
	echo 'URL: '.$url = 'http://zeroguess.net/010/icuerious/crawlTest.php';
	// echo file_get_contents($url);
	
	
	$options  = array('http' =>array('user_agent' => $_SERVER['HTTP_USER_AGENT']));
	$context  = stream_context_create($options);
	echo $response = file_get_contents($url, false, $context)
?>