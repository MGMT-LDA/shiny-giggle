<?php
	include_once("Utility.php");
	include_once("DbHelper.php");
	
	class Users extends DbHelper
	{
		private $dbconnection;
		
		public $userId;
		public $firstName;
		public $lastName;
		public $email;
		public $password;
		public $usertype;
		public $isActive;
		
		public $accessTocken;
		
		function __construct()
		{
			parent::__construct();
		}
		
		
		public function insertUser()
		{
			$data	=	array(
							'first_name'=>$this->firstName,
							'last_name'=>$this->lastName,
							'email'=>$this->email,
							'password'=>md5($this->password),
							'usertype'=>$this->usertype,
							'created'=>date("Y-m-d H:i:s"),
							'is_active'=>$this->isActive
							);
							
			return $this->insert('users',$data);
		}
		
		public function addUserTocken()
		{
			$data	=	array(
							'access_tocken'=>$this->accessTocken
							);
							
			$where	=		array('user_id'=>$this->userId);
			
			if(!empty($this->email))
				$where['email']	=	$this->email;
			
			return $this->update('users',$data, $where);
		}
		
		public function updateUserPassword()
		{
			$data	=	array(
							'access_tocken'=>$this->accessTocken,
							'password'=>md5($this->password),
							);
							
			$where	=		array('user_id'=>$this->userId);
			
			if(!empty($this->email))
				$where['email']	=	$this->email;
			
			return $this->update('users',$data, $where);
		}
		
		public function selectUser()
		{
			$data	=	array();
			
			if(!empty($this->userId))
				$data['user_id']	=	$this->userId;
			
			if(!empty($this->email))
				$data['email']		=	$this->email;
			
			if(!empty($this->password))
				$data['password']	=	md5($this->password);
			
			if(!empty($this->isActive))
				$data['is_active']	=	$this->isActive;
			
			if(!empty($this->accessTocken))
				$data['access_tocken']	=	$this->accessTocken;
			
			return $this->show('users',$data);
		}
	}
?>