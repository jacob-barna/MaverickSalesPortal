<?php
    require '../../classes/UserRepository.php';
    header('Content-type: application/json');

    echo ")]}'\n"; //prevent json hijacking https://docs.angularjs.org/api/ng/service/$http (see security considerations section)

    
   // $params = json_decode(file_get_contents('php:://input'));
    //echo ($_POST["userName"]);
    
    if(UserRepository::AuthorizeUser(htmlspecialchars($_POST["userName"]), htmlspecialchars($_POST["password"])) == 1) {
        echo("[{\"isValid\":\"true\"}]");
    } else {
        echo("[{\"isValid\":\"false\"}]");
    };
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    
    ?>

