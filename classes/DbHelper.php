<?php
	
	class DbHelper
	{
		var $lastInsertedId	=	'';
		var $customer_id	=	'';
		
		private $dbconnection;
		
		function __construct()
		{
			$mysqli_obj = new Database();
			$this->dbconnection=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
			$this->ipAddress=$_SERVER['REMOTE_ADDR'];
		}
		
		function insert($tbl_name,$data)
		{
			$fields=array();
			$values=array();
			foreach($data as $name=>$value)
			{
				if($value	=='')
				{
					$pre	=	"'";
					$post	=	"'";
					$value	=	$pre.addslashes($value).$post;
				}
				else
				{
					$pre	=	"'";
					$post	=	"'";
					$value	=	$pre.addslashes($value).$post;
				}
				$fields[]	=	$name;
				$values[]	=	$value;
			}
			
			$sql="INSERT INTO $tbl_name (".implode(', ',$fields).') VALUES ('.implode(', ',$values).')';
			mysqli_query($this->dbconnection,$sql);
			return $this->lastInsertedId = mysqli_insert_id($this->dbconnection);
		}
		
		function update($tbl_name,$data,$where)
		{
			$fields=array();
			$values=array();
			foreach ($data as $key => $val)
			{
				$pre	=	"'";
				$post	=	"'";
				$valstr[] = $key.' = '.$pre.addslashes($val).$post;
			}
			
			foreach ($where as $key => $val)
			{
				$pre	=	"'";
				$post	=	"'";
				$wheretr[] = $key.' = '.$pre.addslashes($val).$post;
			} 
			$sql	= 'UPDATE '.$tbl_name.' SET '.implode(', ', $valstr).' where '. implode('and ', $wheretr);
			return mysqli_query($this->dbconnection,$sql);
		}
		
		function delete($tbl_name,$where)
		{
			foreach ($where as $key => $val)
			{
				$pre	=	"'";
				$post	=	"'";
				$wheretr[] = $key.' = '.$pre.$val.$post;
			} 
			$sql	= 'Delete From '.$tbl_name.'  where '. implode('and ', $wheretr);
			mysqli_query($this->dbconnection,$sql);
		}
		
		
		function show($tbl_name,$where)
		{
			foreach ($where as $key => $val)
			{
				$pre	=	"'";
				$post	=	"'";
				$wheretr[] = $key.' = '.$pre.$val.$post;
			} 
			$sql	= 'SELECT * FROM '.$tbl_name.'  WHERE '. implode(' AND ', $wheretr);
			$result = mysqli_query($this->dbconnection,$sql);
			return $result= $this->getallData($result);
		}
		
		
		function getallData($pQuery)
		{
			if(mysqli_num_rows($pQuery)>0)
			{
				while($res=mysqli_fetch_object($pQuery))
				{
					$result[]=$res;
				}
			}
			else
			{
				$result=array();
			}
			return $result;
		}
	}

?>