<?php
    require '../../classes/SalesRepository.php';
    require '../../classes/LineItem.php';
    require '../../classes/Item.php';
    require '../../classes/SalesOrder.php';
    header('Content-type: application/json');
    
    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)
  
    
    // $params = json_decode(file_get_contents('php:://input'));
    //echo ($_POST["FirstName"]);
    
    /*
    $CustomerId = htmlspecialchars($_POST["CustomerId"]);
    $ShipAddress = htmlspecialchars($_POST["ShipAddress"]);
    $ListOfItemsToOrder = array(
                            new Item(htmlspecialchars($_POST["Id"]),
                                     htmlspecialchars($_POST["Name"]),
                                     htmlspecialchars($_POST["Price"])
                                    ), 
                            htmlspecialchars($_POST["QtyOrdered"]),
                            htmlspecialchars($_POST["UnitPrice"])
                            );
     * 
     */
    
    $CustomerId = htmlspecialchars($_POST["custId"]); 
    $ShipDate = date('Y-m-d', strtotime(urldecode($_POST["shipDate"])));
    $ListOfItemsToOrder = array();
        
    $lineItemArray = $_POST["lineItems"];
    
    //we need to map our object to a line item
    foreach($lineItemArray as $lineItem) {
        
        array_push($ListOfItemsToOrder, new LineItem(new Item($lineItem["Id"]), $lineItem["Qty"]));
    }
    
    $OrderId = SalesRepository::CreateOrder($CustomerId, $ShipDate, $ListOfItemsToOrder);
    
    echo("{\"orderId\":\"$OrderId\"}");
 /*   
    if(CustomerRepository::CreateOrder($CustomerId, $ShipDate, $ListOfItemsToOrder) == true) {
        echo("[{\"isSuccess\":\"true\"}]");
    } else {
        echo("[{\"isSuccess\":\"false\"}]");
    };
*/
/* ($FirstName, $LastName, $BillAddress, $ShipAddress, $ContactInfo) 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



?>