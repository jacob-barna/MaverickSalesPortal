<?php
    require '../../classes/CustomerRepository.php';
    require '../../classes/Customer.php';
    require '../../classes/State.php';
    require '../../classes/Address.php';
    require '../../classes/ContactInfo.php';
    header('Content-type: application/json');
    
    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    
    // $params = json_decode(file_get_contents('php:://input'));
    //echo ($_POST["FirstName"]);
    
    $CustomerObject = new Customer(htmlspecialchars($_POST["CustomerName"]),
                            new Address(htmlspecialchars($_POST["BillAddress_Line1"]), 
                                htmlspecialchars($_POST["BillAddress_Line2"]), 
                                htmlspecialchars($_POST["BillAddress_City"]), 
                                htmlspecialchars($_POST["BillAddress_State"]), 
                                htmlspecialchars($_POST["BillAddress_Zip"]) ), 
                            new Address(htmlspecialchars($_POST["ShipAddress_Line1"]), 
                                htmlspecialchars($_POST["ShipAddress_Line2"]), 
                                htmlspecialchars($_POST["ShipAddress_City"]), 
                                htmlspecialchars($_POST["ShipAddress_State"]), 
                                htmlspecialchars($_POST["ShipAddress_Zip"]) ),
                            new ContactInfo(htmlspecialchars($_POST["ContactInfo_Phone"]), 
                                htmlspecialchars($_POST["ContactInfo_Fax"]),
                                htmlspecialchars($_POST["ContactInfo_Email"]),
                                htmlspecialchars($_POST["ContactInfo_Webaddress"]))    
                            );
    
    
    if(CustomerRepository::EditCustomer($CustomerObject) == 1) {
        echo("[{\"isSuccess\":\"true\"}]");
    } else {
        echo("[{\"isSuccess\":\"false\"}]");
    };

/* ($FirstName, $LastName, $BillAddress, $ShipAddress, $ContactInfo) 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



?>