<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
        <?php  
        $conn = mysqli_connect("localhost","root","root");
       $db = mysqli_select_db($conn,"SalesPortal");
        if(mysqli_connect_error())
        {
            echo"Failed to connect to mysql:".mysqli_connect_error();
        }
       ?>
      
        <?php
            
             $email = $_POST['Email'];
             $pass =$_POST['Password'];
              $fname = $_POST['FirstName'];
              $lname =$_POST['LastName'];
               
               $sql = "insert into User (Email,Password,FirstName,LastName) values ('".$email."','".$pass."','".$fname."','". $lname."')";
              
              $qury = mysqli_query($conn,$sql);
              if(!$qury)
              {
                  echo 'Failed To Register';
              }
               else {
                  echo 'You Are Succesfully Registered';
                    }
                    echo '<br>';     
      
        echo '<a href="http://localhost/own/RegisterForm.php"> Register </a>';
              
        ?>
    </body>
</html>