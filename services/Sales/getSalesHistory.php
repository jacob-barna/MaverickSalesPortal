<?php
    require '../../classes/SalesRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

  // $ItemName = "seat"; 
   $historyRecords = SalesRepository::GetSalesHistory(htmlspecialchars($_POST["startDate"]), htmlspecialchars($_POST["endDate"]), htmlspecialchars($_POST["custId"]));
   //$item =  ItemRepository::getItemsByName($ItemName);
   echo json_encode($historyRecords);
    
    ?>