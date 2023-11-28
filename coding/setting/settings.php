<?php
session_start();
$state ="";
$username = $_SESSION['user'];
$connection = pg_connect("host=localhost dbname=candle user=postgres password=abc-123");
if (!$connection) {
    exit;
} else {        
    $sql = "Select year from student where user_name = '$username'";
    $result = pg_query($connection,$sql);
    if (!$result){
        exit;
    } else {
        $row = pg_fetch_assoc($result);  
        $year = $row['year'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>UWT elektro</title>
<meta name="viewport" content="width=device-width,
	initial-scale=1, shrink-to-fit=no"> 
<link href="style_setting.css" rel="stylesheet">
</head>
<body>
    <header>Settings</header>
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
    <?php 
    if (isset($_POST['changePasswordBtn'])){
        if (isset($_POST['oldpassword']) && isset($_POST['newpassword'])){        
            $connection = pg_connect("host=localhost dbname=candle user=postgres password=abc-123");
            if (!$connection) {
                exit;
            } else {        
                $sql = "Select password from student where user_name = '$username'";
                $result = pg_query($connection,$sql);
                if (!$result){                    
                    exit;
                } else {
                    $row = pg_fetch_assoc($result);                    
                    if ($row['password']==$_POST['oldpassword']){
                        $newpass = $_POST['newpassword'];
                        $sql = "update student set password = '$newpass' where user_name = '$username'";
                        $result = pg_query($connection,$sql);
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
    }       
?>    
</form>        
</body>
<footer>
    <?php include('../templates/footer.php'); ?>    
</footer>
</html>
