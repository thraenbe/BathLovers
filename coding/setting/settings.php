<?php
session_start(); 
include('../util/db.php'); 
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Settings</title>
<meta name="viewport" content="width=device-width,initial-scale=1, shrink-to-fit=no"> 
<link  rel="stylesheet" href="/style_setting.css">
</head>
<body>
    <?php
    $state ="";
    if (isset($_SESSION['user'])){        
        $username = $_SESSION['user'];
        $sql = "SELECT year FROM student WHERE user_name = '$username'";
        $result = pg_query($dbconn,$sql);
        if (!$result){
            exit;
        } else {
            $row = pg_fetch_assoc($result);  
            $year = $row['year'];
        }
    ?>
    <img id="userProfile" src="ProfileW.jpg" alt="User Profile Photo">
    <!-- User Name -->
    <div id="userName"><?php echo "$username" ?></div>
    <div class = "specs"><?php echo "graduation year: $year"?></div>
    <div class = "specs"><?php echo "$username@uniba.com" ?></div>
    <form action="" method="post">
    <!-- Change Password Button -->    
    <!-- Password Input -->
    <label for="heslo">Old password:</label><br>
    <input type="password" id="oldpassword" name="oldpassword" class="input"><br>    
    <label for="heslo">New password:</label><br>
    <input type="password" id="newpassword" name="newpassword" class="input"><br>
    <!-- change pass button     -->
    <input id="changePasswordBtn" type="submit" class="changeBtn" name="changePasswordBtn" value="Change Password"  ><br>          
</form>
<form action="../login/login.php" method="post">
    <input name="logout" type="submit" id="odhlas" value="Logout" class="logoutbtn">                
</form>
<?php 
}
else {
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p>";
    
}

if (isset($_POST['changePasswordBtn'])){
    if (isset($_POST['oldpassword']) && isset($_POST['newpassword'])){                    
        $sql = "SELECT password FROM student WHERE user_name = '$username'";
        $result = pg_query($dbconn,$sql);
        if (!$result){                    
            exit;
        } else {
            $row = pg_fetch_assoc($result);                    
            if ($row['password']==$_POST['oldpassword']){
                $newpass = $_POST['newpassword'];
                $sql = "UPDATE student SET password = '$newpass' WHERE user_name = '$username'";
                $result = pg_query($dbconn,$sql);
                if($result ){
                    $_POST = array();
                    echo "<div id='oznam' style='color: grey;'>Password sucessfully changed :-> </div>";
                }                                                     
            } else{
                echo "<div id='oznam' style='color: grey;'>Bad old password :-< </div>";
            }
        }            
    }        
}
 else if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
}

?>
</body>

<footer>
    <?php include('../templates/footer.php'); ?>    
</footer>
</html>
