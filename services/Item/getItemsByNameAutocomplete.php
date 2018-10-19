<?php
    require '../../classes/ItemRepository.php';
    header('Content-type: application/json');

   // echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    //this file exists only because angucomplete plugin wants root object
   $response = array( "results" => ItemRepository::getItemsByName(htmlspecialchars($_GET["ItemName"])) );
   echo json_encode($response);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
    ?>