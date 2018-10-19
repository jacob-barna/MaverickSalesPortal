<?php
    require '../../classes/ItemRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

  // $ItemName = "seat"; 
   $item =  ItemRepository::getItemsByName(htmlspecialchars($_GET["ItemName"]));
   //$item =  ItemRepository::getItemsByName($ItemName);
   echo json_encode($item);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
    ?>