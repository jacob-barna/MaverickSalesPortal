<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require 'GenericRepository.php';
require 'EmailService.php';
require 'User.php';

    class UserRepository{
    
        public static function getConnection_UserRepository(){
            $db = GenericRepository::getConnection();
           
        }

//Make a method named AuthorizeUser in the User Repository that selects a user by user name and returns bool. 
// True if the user is marked as Active and Approved.  False otherwise. 
        public static function AuthorizeUser($userName,$password)
        {
            $db = GenericRepository::getConnection();
            
            //note this is a fake school projects implementation - not secure
            $hashedPassword = md5($password);

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT 1 FROM User
                    WHERE Email=? and
                        Password=? and Active=1 and Approved=1"
                ))
                {
                    $query->bind_param('ss',$userName,$hashedPassword);
                    if($result=$query->execute())
                    {
                        $query->bind_result($exists);
                        $query->fetch();
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
            
            return !is_null($exists) && $exists==1?true:false;

        }
        
        public static function RegisterUser($userName,$password,$firstName,$lastName)
        {
            
            if(self::UserExists($userName)) 
            {
                return false;
            }
            
            $db = GenericRepository::getConnection();
            
             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            //don't register the user if they already exist.

            
            try
            {
                if($query = $db->prepare("INSERT INTO User (Email, Password, FirstName, LastName, LinkId, Active)"
                        . " VALUES (?,?,?,?,uuid(),0)"
                        ))
                {
                
                    $query->bind_param('ssss',$userName,  md5($password),$firstName,$lastName);
                    if($result=$query->execute())
                    {
                        // $query->bind_result($success);
                        
                        // store into variable count the number of affacted rows 
                        $count = $query->affected_rows; 
                        $query->fetch();
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
            
            EmailService::sendRegistrationEmailToSupervisors($firstName, $lastName, $userName);
            
            return !is_null($count) && $count==1?true:false;          
        }
      
        
        public static function UserExists($userName)
        {
          //return true if a user has this email address in the database
          //return false otherwise 
        $db = GenericRepository::getConnection();

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            
            
            try
            {
                if($query = $db->prepare("SELECT 1 FROM User
                    WHERE Email=?"
                ))
                {
                    $query->bind_param('s',$userName);
                    if($result=$query->execute())
                    {
                        $query->bind_result($exists);
                        $query->fetch();
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
            
            return !is_null($exists) && $exists==1?true:false;
        
        }
        
        public static function GetUser($userName,$isApproved,$isActive)
        {
          //return true if a user has this email address in the database
          //return false otherwise 
        $db = GenericRepository::getConnection();
        
        $user = new User();
        $intIsApproved = $isApproved == true ? 1 : 0;
        $intIsActive = $isActive == true ? 1 : 0;

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT Id, Email, Password, FirstName, LastName, Supervisor, Approved, Active
                    , LinkId FROM User WHERE Email=? AND Approved=? AND Active=?" 
                ))
                {
                    $query->bind_param('sii', $userName,$intIsApproved,$intIsActive);
                    if($result=$query->execute())
                    {
                        $query->bind_result($Id, $Email, $Password, $FirstName, $LastName, $Supervisor, $Approved, $Active
                    , $LinkId);
                        $query->fetch();
                        $query->close();
                        
                        $user->Id = $Id;
                        $user->Email = $Email;
                        $user->Password = $Password;
                        $user->FirstName = $FirstName;
                        $user->LastName = $LastName;
                        $user->Supervisor = $Supervisor;
                        $user->Approved = $Approved;
                        $user->Active = $Active;
                        $user->LinkId = $LinkId;
                        
                       
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
            
            return $user;
        
        }
        
        public static function GetUsers($isApproved,$isActive)
        {
          //return true if a user has this email address in the database
          //return false otherwise 
        $db = GenericRepository::getConnection();
        
        $users = array();
        $intIsApproved = $isApproved == true ? 1 : 0;
        $intIsActive = $isActive == true ? 1 : 0;
      

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT Id, Email, Password, FirstName, LastName, Supervisor, Approved, Active, LinkId FROM User WHERE Approved=? AND Active=?"))
                {
                   // $query-> Call to undefined method mysqli_stmt::debugDumpParams()
                    $query->bind_param('ii',$intIsApproved,$intIsActive);
                    if($result=$query->execute())
                    {

                        $query->bind_result($Id, $Email, $Password, $FirstName, $LastName, $Supervisor, $Appr, $Act, $LinkId);
                        // $query->fetch();
                        while($query->fetch()){
                            array_push($users,new User($Id,$Email,$FirstName,$LastName,$Appr,$Act,$Supervisor,$LinkId));
                        }
                            
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
            
            return $users;
        
        }
        
        public static function GetUserByLinkId($lnkId)
        {
          //return true if a user has this email address in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();

            $user = new User();

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                
                if($query = $db->prepare("SELECT Id, Email, Password, FirstName, LastName, Supervisor, Approved, Active
                    , LinkId FROM User WHERE LinkId=?" 
                ))
                {
                    $query->bind_param('s', $lnkId);
                    
                    if($result=$query->execute())
                    {
                        self::MapUserResultToUserObject($query, $user);
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
            
            return $user;
        
        }
        
        //stuff like this maybe should go in a separate mapping class.
        private static function MapUserResultToUserObject($thisQuery, $thisUser)
        {
                $thisQuery->bind_result($Id, $Email, $Password, $FirstName, $LastName, $Supervisor, $Approved, $Active
                    , $LinkId);
                        $thisQuery->fetch();
                        $thisQuery->close();
                        
                        $thisUser->Id = $Id;
                        $thisUser->Email = $Email;
                        $thisUser->Password = $Password;
                        $thisUser->FirstName = $FirstName;
                        $thisUser->LastName = $LastName;
                        $thisUser->Supervisor = $Supervisor;
                        $thisUser->Approved = $Approved;
                        $thisUser->Active = $Active;
                        $thisUser->LinkId = $LinkId;
        }
        
        public static function GetSupervisors() {
		$db = GenericRepository::getConnection();
		$supervisors = array();
                mysqli_report(MYSQLI_REPORT_STRICT);
		if($result = $db->query("SELECT Id, Email, FirstName, LastName From User WHERE Active=1 AND Supervisor=1"))
		{
			while($row = $result->fetch_assoc()) {
				array_push($supervisors, new User($row["Id"], $row["Email"], $row["FirstName"], $row["LastName"]));
			}
	
			$result->free();
		}
		$db->close();
	
		return $supervisors;
	}
        
        public static function UpdateUser(User $u) {
            //run update statement
            $db = GenericRepository::getConnection();
            
             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            
//$u->Email
            
            try
            {
                if($query = $db->prepare("UPDATE User set Email=?, FirstName=?, LastName=?, "
                        . "Approved=?"
                        . " where Id=?"
                        ))
                {
                    $query->bind_param('sssii', $u->Email, $u->FirstName,$u->LastName, $u->Approved, $u->Id);
                    if($result=$query->execute())
                    {
                        // $query->bind_result($success);
                        
                        // store into variable count the number of affacted rows 
                        $count = $query->affected_rows; 
                        $query->fetch();
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
        }
        
        public static function ApproveUser($requestorEmail, $supervisorEmail, $supervisorPassword) {
            
            // check to see if this person is supervisor and his credential is valid 
            // If not valid, returns false
            if(!self::AuthorizeSupervisor($supervisorEmail,$supervisorPassword))  
                return false;

            // If valid, updates the requestor setting isApprove = 1 
            // If update is successful, returns true
            $user = self::GetUser($requestorEmail,false,false);
            if($user->Id == 0)
                return false;
            
            $user->Approved = 1;
            try
            {
                self::UpdateUser($user);
            }
            catch (Exception $e)
            {         
                return false;
            }    
            //todo: this code actually works, but we need to use only our own email addresses or get production access.
            //EmailService::sendRegistrationApprovedEmail($user->FirstName, $user->LastName, $user->Email, $user->LinkId);
            
            return true;
            
        }
        
        public static function RejectUser($id, $supervisorEmail, $supervisorPassword)
        {
            if(!self::AuthorizeSupervisor($supervisorEmail,$supervisorPassword))  
                 return false;
            
            $db = GenericRepository::getConnection();
            $count = 0;
            
            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            try
            {
                if($query = $db->prepare("DELETE FROM User
                    WHERE Id=?"
                ))
                {
                    $query->bind_param('i',$id);
                    if($result=$query->execute())
                    {
                        // store into variable count the number of affacted rows 
                        $count = $query->affected_rows; 
                        $query->fetch();
                        $query->close();
                    }
                }
            }
            catch (Exception $e)
            {
                return false;
               
            }
            finally
            {
                $db->close();
            }

            if($count > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        public static function AuthorizeSupervisor($userName,$password)
        {
            $db = GenericRepository::getConnection();
            
            $hashedPassword = md5($password);

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT 1 FROM User
                    WHERE Email=? and
                        Password=? and Active=1 and Approved=1 and Supervisor = 1"
                ))
                {
                    $query->bind_param('ss',$userName,$hashedPassword);
                    if($result=$query->execute())
                    {
                        $query->bind_result($exists);
                        $query->fetch();
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
            
            return !is_null($exists) && $exists==1?true:false;
        }
        
        public static function activateUser($linkId) {
             $db = GenericRepository::getConnection();
            
            $user = self::GetUserByLinkId($linkId);
            if($user->Id == 0) //did not find by this link id so couldn't activate 
                return false;
            
            //if they already activated themselves just return success     
            if($user->Active == 1)
                return true;
            
            //set them active since we now know we found them and they weren't already active
            $user->Active = 1;
            
            try
            {
                self::UpdateUser($user);
            }
            catch (Exception $e)
            {         
                return false;
            }    
            
            return true;
        }
       
         public static function GetUnapprovedUsers() {
            
           return self::GetUsers(false,false);
            
            
        }
    }
  

?>