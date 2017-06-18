<?php
require_once('dbconfig.php');

class USER
{
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
    }
	
	public function runQuery($sql)
	{
		$stmt = $this->conn->prepare($sql);
		return $stmt;
	}
	
	public function register($uname,$uemail,$upass)
	{
		try
		{
			# encrypt password using password_hash
			$new_password = password_hash($upass, PASSWORD_DEFAULT);
			
			$stmt=$this->conn->prepare("INSERT INTO users (username, useremail, userpassword)
    		VALUES(:uname, :uemail, :upass)");
			$stmt->bindParam(':uname', $uname);						  	
			$stmt->bindParam(':uemail', $uemail);						  	
			$stmt->bindParam(':upass', $new_password);						  	
			$stmt->execute();
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

	public function update_user($uname,$uemail,$id)
	{
		try
		{	
			$stmt=$this->conn->prepare("UPDATE users SET
				username=:uname,
				useremail=:uemail
    			WHERE id=:id");
			$stmt->bindparam(":uname", $uname);						  	
			$stmt->bindparam(":uemail", $uemail);					  	
			$stmt->bindparam(":id", $id);						  	
			$stmt->execute();
			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

	public function updatepassword($id,$upass)
	{
		try
		{	
			$new_password = password_hash($upass, PASSWORD_DEFAULT);

			$stmt = $this->conn->prepare("UPDATE users SET
				userpassword=:upass
    			WHERE id=:id");
			$stmt->bindparam(":upass", $new_password);					  	
			$stmt->bindparam(":id", $id);						  	
			$stmt->execute();

			return $stmt;	
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}

	public function delete_user($id)
	{
		try
		{
			$stmt=$this->conn->prepare("UPDATE users SET deleted=:val WHERE id=:id");
			$stmt->execute(array(":val"=>1, ":id"=>$id));
			return $stmt;
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function doLogin($uname,$uemail,$upass)
	{
		try
		{
			# generates new session id every login
			session_regenerate_id(true);

			$stmt = $this->conn->prepare("SELECT id, username, useremail, userpassword FROM users WHERE username=:uname OR useremail=:uemail AND deleted=:val");
			$stmt->execute(array(':uname'=>$uname, ':uemail'=>$uemail, ':val'=>0));
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
			# if username and email exists, verify password
			if($stmt->rowCount() == 1)
			{
				if(password_verify($upass, $userRow['userpassword']))
				{
					$_SESSION['user_session'] = $userRow['id'];
					return true;
				}
				else
				{
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function is_loggedin()
	{
		if(isset($_SESSION['user_session']))
		{
			return true;
		}
	}
	 
	public function redirect($url)
	{
		header("Location: $url");
	}
	
	public function doLogout()
	{
		session_destroy();
		unset($_SESSION['user_session']);
		return true;
	}
}
?>