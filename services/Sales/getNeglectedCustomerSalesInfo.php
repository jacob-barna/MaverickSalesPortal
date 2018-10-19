<?php
    require '../../classes/SalesRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

   $yearlySalesInfoRecords = SalesRepository::GetNeglectedCustomerSalesInfo($_POST["limitTo"]);
   echo json_encode($yearlySalesInfoRecords);
    
    ?>