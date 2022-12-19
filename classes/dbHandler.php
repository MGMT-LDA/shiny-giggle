<?php
   
   /** 
#   +----------------------------------------------------------------------+
#   |            This class handles the requests made to Mysql database     |
#   +----------------------------------------------------------------------+
#   | Copyright (c) 2009-present Teckbiz.com.						       |
#   +----------------------------------------------------------------------+
#   | The contents of this source file is the sole property of             |
#   | Teckbiz. Unauthorized duplication          |
#   | or access is prohibited.                                             |
#   +----------------------------------------------------------------------+
#   | Authors: Rohit Vij  <info@teckbiz.com>                               |
#   |          Rohit Bhatia <info@teckbiz.com>		                       |
#   +----------------------------------------------------------------------+
#   | Revision Date and Comments:                                          |
#   | Date:                                                                |
#   | Comments:                                                            |
#   +----------------------------------------------------------------------+
#
**/
   

   class DataBase
    {
       var $dbHostName="";
	   var $dbUserName="";
	   var $dbPassword="";
	   var $dbName="";
	   var $dbLink='';
	   var $resultResource="";
	   var $totalRows="";
	   
	   //constructor of the class
	   
	   function DataBase()
	   {
	   }
	   
	   function DataBase_Mysqli($hName,$hUser,$hPassword,$hdb)
	    {
		  global $dbconn;
		  $this->dbHostName=$hName;
		  $this->dbUserName=$hUser;
		  $this->dbPassword=$hPassword;
		  $this->dbName=$hdb;
		  
		  if(empty($dbconn)	&&	(!is_object($dbconn)	||	!mysqli_ping($dbconn)))
		  {
			  $this->dbLink=mysqli_connect($this->dbHostName,$this->dbUserName,$this->dbPassword) or die("Not able to connect to database".mysqli_error());
			  mysqli_select_db($this->dbLink,$this->dbName) or die("Unknown Database ".$this->dbName."  ".mysqli_error($this->dbLink));
			  $dbconn = $this->dbLink;
		  }
		  else
			  $this->dbLink = $dbconn;
		  
		  return $this->dbLink;
		} 
		
		// function executing query		
		function query($qryString)
		 {
		   $this->resultResource=mysqli_query($this->dbLink,$qryString) or die("Not able to execute the query: ".$qryString."  ".mysqli_error($this->dbLink));
		   $this->setMaxId(mysqli_insert_id($this->dbLink));
		   return $this->resultResource;
		 }
		
		// functions to set and get the auto incremented id of latest record inserted in the table in case of insert query 
	  function setMaxId($id)
		{
		  $this->lastInsertedId	=	$id;
		}
	
	
	   // function to get the Max id set by setMaxId function
	   function getMaxId()
		{
		  return $this->lastInsertedId;
		}	
	  //-------------------------------------------------------------------------------------------------------------------
	  
	  
	  // Function provides the total number of records return by a select query.
	  function getTotalRows($resource)
	   {
	     return $this->totalRows=mysqli_num_rows($resource);
	   }
	   
	  // function provides the object to access the result records of the select query. 
	  function getResultObject($resource)
	   {
	     return $this->rowObj=mysqli_fetch_object($resource);
	   }
	   
	   // function provides the row wise result records after executing the select query
	   function getResultRow($resource)
	   {
	     return $this->rowObj=mysqli_fetch_row($resource);
	   }
	   
	   	function loadObjectList() 
		{
			$array = array();
			while ($row = mysqli_fetch_object( $this->resultResource )) 
			{
				$array[] = $row;
			}
			return $array;
		}

	   // function provides the result in the form of an array
	   function getResultArray($resource)
	   {
	     return $this->rowObj=mysqli_fetch_array($resource,MYSQLI_ASSOC);
	   }
	   
	   // function provides the result in the form of an associative array
	   function getResultAssoc($resource)
	   {
	     return $this->rowObj=mysqli_fetch_assoc($resource);
	   }
	   
	   //function to close the db connection.
	   static function closeDbConnection()
	    {
			global $dbconn;
			mysqli_close($dbconn);
			$dbconn = '';
		}
	  	
    }
   
?>