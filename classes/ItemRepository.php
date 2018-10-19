<?php

require 'GenericRepository.php';
require 'Item.php';

class ItemRepository
{

    public static function getItemById($ItemId)
        {
          //return true if a user has this email address in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();
        
            $Item = new Item();
        
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            try
            {
                if($query = $db->prepare("SELECT Id, Name, Price FROM Item
                    WHERE Id=?"
                ))
                {
                    $query->bind_param('i',$ItemId);
                    if($result=$query->execute())
                    {
                        $query->bind_result($Id, $Name, $Price);
                        $query->fetch();
                        $query->close();
                        
                        $Item->Id = $Id;
                        $Item->Name = $Name;
                        $Item->Price = $Price;
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
                
	    return $Item;
	}

       public static function getItemsByName($ItemName)
        {
         
            $db = GenericRepository::getConnection();
        
            $Items = array();
        
            mysqli_report(MYSQLI_REPORT_STRICT);
            $ItemSearch = "%".$ItemName."%";
            try
            {
                if($query = $db->prepare("SELECT Id, Name, Price FROM Item
                    WHERE Name LIKE ?"
                ))
                {
                    $query->bind_param('s',$ItemSearch);
                    if($result=$query->execute())
                    {

                        $query->bind_result($Id, $Name, $Price);
                        // $query->fetch();
                        while($query->fetch()){
                            array_push($Items,new Item($Id,$Name,$Price));
                        }
                            
                        $query->close();
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
                
	    return $Items;
	}
}

          
        
?>