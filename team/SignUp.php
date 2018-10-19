<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        
        <?php  $conn = mysqli_connect("localhost","root","root");
       $db = mysqli_select_db($conn,"SalesPortal");
       session_start();
               ?>
      
        
        <?php
            $email = $_POST['Email'];
            
              $sql = "select * from User where(Email ='".$email."');";
              $qury = mysqli_query($conn,"$sql");
              $result = mysqli_fetch_array($qury);
              
              if($result[0]>0)
              {
                  echo 'Succesfully Logged In';
                  $_SESSION['Email'] = $email;
                  echo '<br>';
                  echo '<br>';
                  echo "Welcome ".$_SESSION['Email']."!";
                  echo '<br>';
                  echo '<br>';
                  echo 'This Email Address Does Exist';
                  echo '<br>';
                 
              }
               else {
                  echo 'Email Address Does not Exist';
                    }
                    echo '<br>';
                    echo '<br>';
                    echo'<a href="http://localhost/team/signform.php">SignUp</a>';
                   
              
        ?>
               </form>
    </body>
</html>