<?php
ob_start();
    header('Content-type: application/json');
    require '../../classes/CustomerRepository.php';
    require '../../classes/Customer.php';
    require_once '../../classes/State.php';
    require '../../classes/Address.php';
    require '../../classes/ContactDetail.php';
    ob_end_clean();
    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)
  
    
    // $params = json_decode(file_get_contents('php:://input'));
    //echo ($_POST["FirstName"]);
    
    $CustomerObject = new Customer(0,htmlspecialchars($_POST["CustomerName"]),
                            new Address(0,htmlspecialchars($_POST["BillAddress_Line1"]), 
                                isset($_POST["BillAddress_Line2"]) ? htmlspecialchars($_POST["BillAddress_Line2"]) : null, 
                                htmlspecialchars($_POST["BillAddress_City"]), 
                                new State(htmlspecialchars($_POST["BillAddress_State"]),null,null), 
                                htmlspecialchars($_POST["BillAddress_Zip"]) ), 
                            new Address(0,htmlspecialchars($_POST["ShipAddress_Line1"]), 
                                 isset($_POST["ShipAddress_Line2"]) ? htmlspecialchars($_POST["ShipAddress_Line2"]) : null, 
                                htmlspecialchars($_POST["ShipAddress_City"]), 
                                new State(htmlspecialchars($_POST["ShipAddress_State"]),null,null), 
                                htmlspecialchars($_POST["ShipAddress_Zip"]) ),
                            new ContactDetail(0,htmlspecialchars($_POST["ContactInfo_Phone"]), 
                                isset($_POST["ContactInfo_Fax"]) ? htmlspecialchars($_POST["ContactInfo_Fax"]) : null,
                                isset($_POST["ContactInfo_Email"]) ? htmlspecialchars($_POST["ContactInfo_Email"]) : null,
                                isset($_POST["ContactInfo_Webaddress"]) ? htmlspecialchars($_POST["ContactInfo_Webaddress"]) : null)    
                            );
    
    
    if(CustomerRepository::CreateCustomer($CustomerObject) == true) {
        echo("[{\"isSuccess\":\"true\"}]");
    } else {
        echo("[{\"isSuccess\":\"false\"}]");
    };


?>