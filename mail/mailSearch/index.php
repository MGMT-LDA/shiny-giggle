<?php
ini_set('display_errors',1);
$to="vandnakapoor19@gmail.com";
$fn="vandna";
$ln="Last Name";
$name=$fn.' '.$ln;
$subject = "Welcome to Website";
$message = "Dear $name, 


Your Welcome Message.


Thanks
www.website.com
";
include('smtpwork.php');

?>