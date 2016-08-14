<?php
# ========================================================================#
#
#  Database object which holds the conection with the database (we only want one)
#
# ========================================================================#

require_once("initialize.php");
require_once("sanitize.php");

class MySQLDatabase
{
	
	private $connection;
	
	private $db_host = 'localhost';
	private $username = 'RegistrationForm_user';
	private $pwd = '@RegistrationForm12passWrd';
	private $database = 'RegistrationForm';
	
	
	// --------------------------------------------------
	// open connection to database
	// --------------------------------------------------
	function __construct()
	{
		$this->open_connection();
	}
	
	
	// --------------------------------------------------
	// open database connection
	// --------------------------------------------------
    function open_connection()
    {
		// 1. Create a database connection
        //$this->connection = mysqli_connect($this->db_host,$this->username,$this->pwd,$this->database);
		
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		
		if (!$this->connection || mysqli_connect_errno()) {
	        die("Database connection failed: " . 
	             mysqli_connect_error() . 
	             " (" . mysqli_connect_errno() . ")"
	        );
		}

        if(!mysqli_query($this->connection, "SET NAMES 'UTF8'"))
        {
            return false;
        }
		
        return true;
    }
	
		
	
	// --------------------------------------------------
	// get database connection
	// --------------------------------------------------
	public function get_connection()
	{
		return $this->connection;
	}
	
	
	// --------------------------------------------------
	// close the database connection
	// --------------------------------------------------
	public function close_connection()
	{
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	
		
	
	// --------------------------------------------------
	// snanitize data
	// --------------------------------------------------
	public function mysql_prep($string)
	{
		
		$sanitize = new Sanitize();
		$string = $sanitize->clean($string);
		
		$escaped_string = mysqli_real_escape_string($this->connection, $string);
		return $escaped_string;
	}
	
	
	
	// --------------------------------------------------
	// snanitize data if value is set
	// --------------------------------------------------
	public function escape_value($result_set)
	{
		if(isSet($result_set) && $result_set != "")
		{
			return $this->mysql_prep($result_set);
		}
	}
}


$database = new MySQLDatabase();
$db = &$database;

?>