<?php
    require '../../classes/SalesRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

   $quarterSalesInfoRecords = SalesRepository::GetCurrentQuarterSalesInfo($_POST["limitTo"]);
   echo json_encode($quarterSalesInfoRecords);
    
    ?>