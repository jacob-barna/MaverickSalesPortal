<?php
require '../../classes/AddressRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    
   $states =  AddressRepository::getStates();
   echo json_encode($states);



        
?>


