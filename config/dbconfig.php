<?php
class Database
{   
    private $host = "localhost";
    private $db_name = "db_test";
    private $username = "testuser";
    private $password = "";
    public $conn;

    public function dbConnection()
	{
	    $this->conn = null;    
        try
		{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
        }
		catch(PDOException $exception)
		{
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
}

$thisFile = str_replace('\\', '/', __FILE__);
$srvRoot  = str_replace('config/dbconfig.php', '', $thisFile);
$webRoot  = '/addeditdelete/';
$srvpath = '/addeditdelete/';
define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
define('SRV_PATH', $srvpath);
?>
