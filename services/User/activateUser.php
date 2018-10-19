<?php
    require '../../classes/UserRepository.php';
    header('Content-type: application/json');
    
    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    if(UserRepository::activateUser(htmlspecialchars($_POST["linkId"]) ) == 1) {
        echo("[{\"isSuccess\":\"true\"}]");
    } else {
        echo("[{\"isSuccess\":\"false\"}]");
    };

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



?>

