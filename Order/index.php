<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Welcome to Maverick Sales Portal - CRM</title>
        <script src="../scripts/angular.js"></script>
	<script src="../scripts/angular-route.js"></script>
        <script src="../scripts/angular-animate.js"></script>
        <script type="text/javascript" src="../scripts/angular-toastr.tpls.js"></script>
        <script type="text/javascript" src="../scripts/ui-bootstrap-tpls-0.14.3.js"></script>
	<script src="../scripts/OrderApp.js"></script>
       
        <link rel="stylesheet" type="text/css" href="../content/css/bootstrap.css">
        <link rel="stylesheet" type="text/css" href="../content/MaverickSalesPortal.css">
        <link rel="stylesheet" type="text/css" href="../content/angular-toastr.css" />
        
    </head>
    <body data-ng-app="salesPortalOrderApp">
        <?php 
            //require '../vendor/autoload.php';
        ?>
        <div data-ng-view=""></div>
    </body>
</html>
