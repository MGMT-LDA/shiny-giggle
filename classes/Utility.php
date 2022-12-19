<?php include_once("dbHandler.php");?>

<?php
	class Utility
	{
		var $dataTable='';
		var $dataId=0;
		var $description='';
		var $user_id=0;
		var $ipAddress='';
		var $usertype='';
		var $action='';
		var $document_id='';
		var $documenttype='';
		var $consumer_id='';
	//	var $documentaction='';
	//	var $created_id="";
		var $datatableidField="";
		var $useremail="";
		var $isAutomatic='';
		var $dbconnection='';
		
		function Utility()
		{
			$mysqli_obj = new Database();
			$this->dbconnection=  $mysqli_obj->DataBase_Mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
			$this->ipAddress=$_SERVER['REMOTE_ADDR'];
		}
		
		static function debug($pValue="--NO MESSAGE--",$pDie="0",$pColor = "red")
		{
			echo ('<pre><br><font face="verdana" size="1px"  color="'.$pColor.'">START DEBUG CODE<br>');
			print_r($pValue);
			echo ('<br></font></pre>');
			if($pDie==1)
				die;
		}
		
		static function getallData($pQuery)
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
				$result='result not found';
			}
			return $result;
		}
		function logTrack()
		{
		
			$sqlString = "insert into log set 
				dataTable='".$this->dataTable."', 	
				dataId='".$this->dataId. "',
				usertype='".$this->usertype."',
				description='".$this->description. "',
				user_id='".$this->user_id. "',
				action='".$this->action."',
				datatableidField='".$this->datatableidField."',
				ipAddress='".$this->ipAddress. "',
				dateentered=NOW()";
				//echo ($sqlString);
				//die;
			mysqli_query($this->dbconnection,$sqlString);
		}
		
		
		function selectlogTrack()
		{
			if($this->useremail!='')
			{
				$sqlQry="SELECT * FROM tbl_log LOG, tbl_user USER WHERE  USER.isDeleted = 0 AND LOG.user_id = USER.user_id and USER.useremail='".$this->useremail."' and DATE(LOG.dateentered) Between '".$this->start_date."' AND '".$this->end_date."' ORDER BY LOG.dateentered Desc";
				
			}
			else
			{
				$sqlQry="SELECT * FROM tbl_log LOG LEFT OUTER JOIN tbl_user USER  ON  LOG.user_id = USER.user_id and USER.isDeleted = 0 and dateentered>=NOW() - INTERVAL 3 DAY ORDER BY LOG.dateentered Desc ";
				
				
			}

			return mysqli_query($this->dbconnection,$sqlQry);
		}
		
		function documentLogTrack()
		{
				$sqlString = "insert into tbl_documentlog set 
				document_id='".$this->document_id."', 	
				documenttype='".$this->documenttype. "',
				user_id='".$this->user_id."',
				description='".$this->description. "',
				consumer_id='".$this->consumer_id. "',
				ipAddress='".$this->ipAddress. "',
				isAutomatic='".$this->isAutomatic."',
				action='".$this->action."',
				dateentered=NOW()";
				mysqli_query($this->dbconnection,$sqlString);
		}
		
		static function filterString($filterString)
		{
			$stringArr = str_split($filterString);
			$stringCheck = 'abcdefghijklmnopqrstuvwxyz0123456789()?@#-,:&/\\"\'_%${}[]|;.!+=';
			$stringCheckArr = str_split($stringCheck);
			foreach($stringArr as $stringAlphabet)
			{
				$found = 0;
				foreach($stringCheckArr as $stringCheckAlphabet)
				{
					if($stringCheckAlphabet == strtolower($stringAlphabet) || strtolower(trim($stringAlphabet)) == '')
						$found++;
				}
				if($found == 0)
					return $stringAlphabet;
			}
			
			return '';
		}
	}
?>