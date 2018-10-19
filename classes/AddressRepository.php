<?php
require_once 'GenericRepository.php';
require_once 'State.php';



    class AddressRepository { 

        public static function getStates()
        {
          //return true if a user has this email address in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();
        
            $states = array();
        
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            
            if($result = $db->query("SELECT Id, Name, Abbreviation FROM State"))
	    {
                while($row = $result->fetch_assoc()) {
                    array_push($states, new State($row["Id"], $row["Name"], $row["Abbreviation"]));
		}
	
		$result->free();
	    }
	    $db->close();
            
	
	    return $states;
	}
    
        public static function getStateById($StateId)
        {
          //return true if a user has this email address in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();
        
            $state = new State();
        
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            try
            {
                if($query = $db->prepare("SELECT Id, Name, Abbreviation FROM State
                    WHERE Id=?"
                ))
                {
                    $query->bind_param('i',$StateId);
                    if($result=$query->execute())
                    {
                        $query->bind_result($Id, $Name, $Abbreviation);
                        $query->fetch();
                        $query->close();
                        
                        $state->Id = $Id;
                        $state->Name = $Name;
                        $state->Abbreviation = $Abbreviation;
                    }
                }
            }
            catch (Exception $e)
            {
                throw $e;
                return 0;
            }
            finally
            {
                $db->close();
            }
                
	    return $state;
	}

        public static function insertAddress(Address $Address)
        {

            $db = GenericRepository::getConnection();
            
            $insertId = 0;

             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_ALL);

       

            //in the real world, to avoid data duplication,
            // we would check to see if the address existed and just return the id
            //however, we're just going to blindly insert what we got because we don't ahve a lot of time
            try
            {
                if($query = $db->prepare("INSERT INTO Address (Line1, Line2, City, State, Zip5)"
                        . " VALUES (?,?,?,?,?)"
                        ))
                {
                    $query->bind_param('sssis', $Address->Line1, $Address->Line2, $Address->City, $Address->State->Id, $Address->Zip);
                    if($result=$query->execute())
                    {
                        $insertId = $db->insert_id;
                        $query->close();
                    }

                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
            finally
            {
                $db->close();
            }

            return $insertId;
        }
        
        
    }    
        
?>
