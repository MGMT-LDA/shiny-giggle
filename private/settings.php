<?php

ob_start();
@session_start();
if ($_SERVER['SERVER_NAME'] == 'localhost')
{
    define("URL", "http://localhost/icuview/");
    define("ADMINURL", "http://localhost/icuview/admin/");
    define("PATH", "http://localhost/icuview/");
    define("ADMINPATH", "http://localhost/icuview/admin/");
    define("DBHOST", "localhost");
    define("DBNAME", "uspatents");
    define("DBUSER", "root");
    define("DBPASS", "");
    define("APP_MODE", "test");
} elseif ($_SERVER['SERVER_NAME'] == 'zeroguess.us' || $_SERVER['SERVER_NAME'] == 'www.zeroguess.us')
{
    define("URL", "http://{$_SERVER['SERVER_NAME']}/n13/icuview/");
    define("ADMINURL", "http://{$_SERVER['SERVER_NAME']}/010/icuerious/admin/");
    define("PATH", "{$_SERVER['DOCUMENT_ROOT']}/n13/icuview/");
    define("ADMINPATH", "{$_SERVER['DOCUMENT_ROOT']}/010/icuerious/admin/");
    define("DBHOST", "localhost");
    define("DBNAME", "dbxudpfmzm2xab");
    define("DBUSER", "u9jclqofmzu1f");
    define("DBPASS", '78b8qma91g6p');
    define("APP_MODE", "live");
}elseif ($_SERVER['SERVER_NAME'] == 'icuerious.com' || $_SERVER['SERVER_NAME'] == 'www.icuerious.com')
{
    define("URL", "https://{$_SERVER['SERVER_NAME']}/icueview/");
    define("ADMINURL", "https://{$_SERVER['SERVER_NAME']}/icueview/");
    define("PATH", "{$_SERVER['DOCUMENT_ROOT']}/icueview/");
    define("ADMINPATH", "{$_SERVER['DOCUMENT_ROOT']}/icueview/admin/");
    define("DBHOST", "localhost");
    define("DBNAME", "icueriou_patent");
    define("DBUSER", "icueriou_patent");
    define("DBPASS", 'DT6KhKTMM8rH');
    define("APP_MODE", "live");
} else
{
    define("URL", "https://{$_SERVER['SERVER_NAME']}/");
    define("ADMINURL", "https://{$_SERVER['SERVER_NAME']}/couponadmin/");
    define("PATH", "{$_SERVER['DOCUMENT_ROOT']}/");
    define("ADMINPATH", "{$_SERVER['DOCUMENT_ROOT']}/couponadmin/");
    define("DBHOST", "mysql51-059.wc1.ord1.stabletransit.com");
    define("DBNAME", "939470_160826_icuerious");
    define("DBUSER", "939470_icuerious");
    define("DBPASS", 'hJk&8^56*');
    define("APP_MODE", "live");
}

define('UPLOAD_DIR',PATH.'application/uploads/');

ini_set('display_errors', 1);
ini_set('max_execution_time', 1800);
ini_set('memory_limit', '-1');


interface PatentTypes
{
	const US 			=	'US';
	const WIPO			=	'WO';
	const ALL			=	'ALL';
}

interface UserType
{
    const ADMIN			=	'ADMIN';
    const CLIENT		=	'CLIENT';
}
?>