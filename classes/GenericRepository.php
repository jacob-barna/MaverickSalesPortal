<?php
	class GenericRepository {
		
            //todo: this class really should be a base class for all other repositories
            //that's how I modeled it... but this works just about the same
		public static function getConnection()
		{
			$servername = "127.0.0.1";
			$username = "root";
			$password = "FAKE_DONT_CHECKIN";
			$database = "SalesPortal";
			$dbport = 3306;
			
			$db = new mysqli($servername, $username, $password, $database, $dbport);
			
			if($db->connect_error){
				die("Connection failed " . $db->connect_error);
			}
			
			//debug only 
			//echo "Connected successfully (" . $db->host_info . ")";
			
			return $db; 
		}
	}
?>