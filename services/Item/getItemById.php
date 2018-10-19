<?php
    require '../../classes/ItemRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    
   //$ItemId = 106; 
   $items =  ItemRepository::getItemById(htmlspecialchars($_GET["Id"]));
   //$item =  ItemRepository::getItemById($ItemId);
   echo json_encode($items);
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
    ?>
