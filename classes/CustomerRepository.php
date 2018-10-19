<?php

require 'GenericRepository.php';
require_once 'Customer.php';
require_once 'ContactDetail.php';
require_once 'Address.php';
require 'SalesOrder.php';
require 'Item.php';
require 'AddressRepository.php';

    class CustomerRepository { 
                    
  
        public static function CreateCustomer (Customer $customer) 
        {

            if(self::CustomerExists($customer->Name)) 
            {
                return false;
            }
            
            $db = GenericRepository::getConnection();
            
             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_ALL);
            
            //don't create the customer if they already exist.
            
            //in the real world, to avoid data duplication,
            // we would check to see if the bill address and ship address exist... if those exist, we will use the ID 
            //similarly, we would've modeled ContactDetail a little differently and done the same with each contact type
            
            //however, we're just going to blindly insert what we got because we don't ahve a lot of time
            
            
            //also in the real world we'd wrap this stuff in a transaction and roll back if one failed 
            $billAddressId = AddressRepository::insertAddress($customer->BillAddress);
            $shipAddressId = AddressRepository::insertAddress($customer->ShipAddress);
            $contactDetailId = self::CreateContactDetail($customer->ContactDetail);
            
            
            if($billAddressId < 1 || $shipAddressId < 1 || $contactDetailId < 1)
            {
                return false;
            }   
           
            
            try
            {
                if($query = $db->prepare("INSERT INTO Customer (Name, Active, ShipAddress, BillAddress, ContactDetail)"
                        . " VALUES (?,1,?,?,?)"
                        ))
                {
                    $query->bind_param('siii',$customer->Name, $billAddressId, $shipAddressId, $contactDetailId);
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
            
           // EmailService::sendRegistrationEmailToSupervisors($firstName, $lastName, $userName);
            
            return !is_null($count) && $count==1?true:false;          
        }
        
         public static function CreateContactDetail (ContactDetail $ContactDetail) 
        {
            $db = GenericRepository::getConnection();
            $insertId = 0;
             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_ALL);
            
            try
            {
                if($query = $db->prepare("INSERT INTO ContactDetail (Phone, Fax, Email, WebAddress)"
                        . " VALUES (?,?,?,?)"
                        ))
                {
                    $query->bind_param('ssss',$ContactDetail->Phone, $ContactDetail->Fax, $ContactDetail->Email, $ContactDetail->WebAddress);
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
        
        public static function GetCustomerByName ($Name)
        {
             $db = GenericRepository::getConnection();
        
            $customer = new Customer();
            $custQry = self::GetCustomerQuery();

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare($custQry
                        . " WHERE C.Name=? "
                ))
                {
                    $query->bind_param('s', $Name);
                    if($result=$query->execute())
                    {
                        $customer = self::MapUserResultToUserObject($query);
       
                    
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
            
            return $customer;
        }
        
        
        public static function GetCustomersByName ($Name)
        {
          //return true if a ccustomer has the same firstname and lastname in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();
        
            $customers = array();
            $NameSearch = "%".strtoupper($Name)."%";
            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare(self::GetCustomerQuery()
                        . "WHERE UPPER(C.Name) LIKE ?"
                ))
                {
                    $query->bind_param('s', $NameSearch);
                    if($result=$query->execute())
                    {
                        $customers = self::MapUserResultsToUserObjectArray($query);
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
            
            return $customers;
        
        }
        
        
        public static function CustomerExists ($Name)
        {
          //return true if a customer has the same firstname and lastname in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();   

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);                  
            
            try
            {
                if($query = $db->prepare("SELECT 1 FROM Customer
                    WHERE Name=?"
                ))
                {
                    $query->bind_param('s',$Name );
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
                return 0;
            }
            finally
            {
                $db->close();
            }
            
            return !is_null($exists) && $exists==1?true:false;
        
        }
        
        
        public static function EditCustomer ($Name, $BillAddress, $ShipAddress, $ContactInfo)
        {
            //run update statement
            $db = GenericRepository::getConnection();
            
            $customers = new Customer();
            
           
            if(!self::CustomerExists($Name)) 
            {
                return false;
            }
            
             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            //$u->Email         
            try
            {
                if($query = $db->prepare("UPDATE Customer set BillAddress=?, ShipAddress=?, ContactInfo=?, "
                        . "where Name=?"
                        ))
                {
                    $query->bind_param('ssss', $BillAddress, $ShipAddress, $ContactInfo, $Name);
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
                return 0;
            }
            finally
            {
                $db->close();
            }
            return true;    
        }     

        public static function GetCustomerById ($Id)
        {
            //return true if a ccustomer has the same firstname and lastname in the database
            //return false otherwise 
            $db = GenericRepository::getConnection();
        
            $customer = new Customer();
            $custQry = self::GetCustomerQuery();

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_ALL);
            try
            {
                if($query = $db->prepare($custQry
                        . " WHERE C.Id=? "
                ))
                {
                    $query->bind_param('i', $Id);
                    if($result=$query->execute())
                    {
                        $customer = self::MapUserResultToUserObject($query);
       
                    
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
            
            return $customer;
        
        }
        
        private static function GetCustomerQuery()
        {
            return "SELECT C.Id, C.Name, "
                        . "bill_Addr.Id as BillAddress_Id, "
                        . "bill_Addr.Line1 as BillAddress_Line1, "
                        . "bill_Addr.Line2 as BillAddress_Line2, "
                        . "bill_Addr.City as BillAddress_City, "
                        . "bill_Addr.State as BillAddress_State_Id, "
                        . "bill_Addr_State.Name as BillAddress_State_Name, "
                        . "bill_Addr_State.Abbreviation as BillAddress_State_Abbrev, "
                        . "bill_Addr.Zip5 as BillAddress_Zip, "
                        . "ship_Addr.Id as ShipAddress_Id, "
                        . "ship_Addr.Line1 as ShipAddress_Line1, "
                        . "ship_Addr.Line2 as ShipAddress_Line2, "
                        . "ship_Addr.City as ShipAddress_City, "
                        . "ship_Addr.State as ShipAddress_State_Id, "
                        . "ship_Addr_State.Name as ShipAddress_State_Name, "
                        . "ship_Addr_State.Abbreviation as ShipAddress_State_Abbrev, "
                        . "ship_Addr.Zip5 as ShipAddress_Zip, "
                        . "cd.Id as ContactDetailId, "
                        . "cd.Phone as ContactDetailPhone, "
                        . "cd.Fax as ContactDetailFax, "
                        . "cd.Email as ContactDetailEmail, "
                        . "cd.WebAddress as ContactDetailWebAddress "
                        . "FROM Customer C "
                        . "JOIN Address ship_Addr ON C.ShipAddress = ship_Addr.Id "
                        . "JOIN State ship_Addr_State ON ship_Addr.State = ship_Addr_State.Id "
                        . "JOIN Address bill_Addr ON C.BillAddress = bill_Addr.Id "
                        . "JOIN State bill_Addr_State ON bill_Addr.State = bill_Addr_State.Id "
                        . "JOIN ContactDetail cd on C.ContactDetail = cd.Id ";
        }
        
        
        private static function MapUserResultToUserObject($thisQuery)
        {
             //todo: we'd have to build a shared object to get rid of this code duplication... not enough time right now 
            $thisQuery->bind_result($CustId, 
                            $CustName, 
                            $BillAddress_Id, 
                            $BillAddress_Line1, 
                            $BillAddress_Line2,
                            $BillAddress_City,
                            $BillAddress_State_Id,
                            $BillAddress_State_Name,
                            $BillAddress_State_Abbrev,
                            $BillAddress_Zip,
                            $ShipAddress_Id,
                            $ShipAddress_Line1,
                            $ShipAddress_Line2,
                            $ShipAddress_City,
                            $ShipAddress_State_Id,
                            $ShipAddress_State_Name,
                            $ShipAddress_State_Abbrev,
                            $ShipAddress_Zip,
                            $ContactDetailId,
                            $ContactDetailPhone,
                            $ContactDetailFax,
                            $ContactDetailEmail,
                            $ContactDetailWebAddress
                            );

                    $thisQuery->fetch();
                    $thisQuery->close();

                    //check to see if the customer bill address, ship address or contact detail is null, and if so, we'll want to 
                    //provide null to the Customer constructor -- otherwise we will present the corresponding object to the Customer Constructor
                    $BillAddressObject = self::MapUserResultAddressToAddressObject($BillAddress_Id,$BillAddress_Line1,$BillAddress_Line2,$BillAddress_City,$BillAddress_State_Id,$BillAddress_State_Name,$BillAddress_State_Abbrev,$BillAddress_Zip);

                    $ShipAddressObject = self::MapUserResultAddressToAddressObject($ShipAddress_Id,$ShipAddress_Line1,$ShipAddress_Line2,$ShipAddress_City,$ShipAddress_State_Id,$ShipAddress_State_Name,$ShipAddress_State_Abbrev,$ShipAddress_Zip);

                    $ContactInfoObject = self::MapUserContactDetailToContactDetailObject($ContactDetailId,$ContactDetailPhone,$ContactDetailFax,$ContactDetailEmail,$ContactDetailWebAddress);

                    return new Customer($CustId, $CustName, $BillAddressObject, $ShipAddressObject, $ContactInfoObject);
        }
        
        private static function MapUserResultsToUserObjectArray($thisQuery)
        {
            mysqli_report(MYSQLI_REPORT_STRICT);
            $customers = array();
            //todo: we'd have to build a shared object to get rid of this code duplication... not enough time right now 
            
            while($thisQuery->fetch()){
                $thisQuery->bind_result(
                                $CustId, 
                                $CustName, 
                                $BillAddress_Id, 
                                $BillAddress_Line1, 
                                $BillAddress_Line2,
                                $BillAddress_City,
                                $BillAddress_State_Id,
                                $BillAddress_State_Name,
                                $BillAddress_State_Abbrev,
                                $BillAddress_Zip,
                                $ShipAddress_Id,
                                $ShipAddress_Line1,
                                $ShipAddress_Line2,
                                $ShipAddress_City,
                                $ShipAddress_State_Id,
                                $ShipAddress_State_Name,
                                $ShipAddress_State_Abbrev,
                                $ShipAddress_Zip,
                                $ContactDetailId,
                                $ContactDetailPhone,
                                $ContactDetailFax,
                                $ContactDetailEmail,
                                $ContactDetailWebAddress
                                );
                
                 $BillAddressObject = self::MapUserResultAddressToAddressObject($BillAddress_Id,$BillAddress_Line1,$BillAddress_Line2,$BillAddress_City,$BillAddress_State_Id,$BillAddress_State_Name,$BillAddress_State_Abbrev,$BillAddress_Zip);

            $ShipAddressObject = self::MapUserResultAddressToAddressObject($ShipAddress_Id,$ShipAddress_Line1,$ShipAddress_Line2,$ShipAddress_City,$ShipAddress_State_Id,$ShipAddress_State_Name,$ShipAddress_State_Abbrev,$ShipAddress_Zip);

            $ContactInfoObject = self::MapUserContactDetailToContactDetailObject($ContactDetailId,$ContactDetailPhone,$ContactDetailFax,$ContactDetailEmail,$ContactDetailWebAddress);
                
                array_push($customers,new Customer($CustId, $CustName, $BillAddressObject, $ShipAddressObject, $ContactInfoObject));
            }
            
            $thisQuery->close();      
            
            return $customers;
        }
        
        private static function MapUserResultAddressToAddressObject($AddressId, $AddressLine1, $AddressLine2, $AddressCity, $AddressStateId, $AddressStateName, $AddressStateAbbrev, $AddressZip)
        {
            $AddressObject = null;
                            
            if($AddressId > 0)
            {
                $AddressObject = new Address($AddressId, 
                        $AddressLine1, 
                        $AddressLine2, 
                        $AddressCity, 
                        new State($AddressStateId, $AddressStateName, $AddressStateAbbrev),
                        $AddressZip);
            }
            
            return $AddressObject;
        }
        
        private static function MapUserContactDetailToContactDetailObject($ContactDetailId, $ContactDetailPhone, $ContactDetailFax, $ContactDetailEmail, $ContactDetailWebAddress)
        {
            $ContactInfoObject = null;
                            
            if($ContactDetailId > 0)
            {
                $ContactInfoObject = new ContactDetail($ContactDetailId,
                        $ContactDetailPhone,
                        $ContactDetailFax,
                        $ContactDetailEmail,
                        $ContactDetailWebAddress
                        );
            }
            
            return $ContactInfoObject;
        }
      
        public static function getAllCustomers()
        {
          //return true if a ccustomer has the same firstname and lastname in the database
          //return false otherwise 
            $db = GenericRepository::getConnection();
        
            $customers = array();

            //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            $custQry = self::GetCustomerQuery();
            try
            {
                if($query = $db->prepare($custQry
                       
                ))
                {
                    if($result=$query->execute())
                    {
                        $customers = self::MapUserResultsToUserObjectArray($query);
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
            
            return $customers;
        
        }
        
    }
    
?>
