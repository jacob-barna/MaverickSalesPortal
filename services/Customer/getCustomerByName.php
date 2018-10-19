<?php
ob_start();
    require '../../classes/CustomerRepository.php';
    header('Content-type: application/json');
ob_end_clean();
    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    $customer =  CustomerRepository::GetCustomerByName(htmlspecialchars($_POST["CustomerName"]));
   echo json_encode($customer);
?>