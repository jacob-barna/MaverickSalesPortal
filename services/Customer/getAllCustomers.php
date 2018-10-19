<?php
ob_start();
    require '../../classes/CustomerRepository.php';
    header('Content-type: application/json');
ob_end_clean();
    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

   $customers =   CustomerRepository::getAllCustomers();
   echo json_encode($customers);
?>