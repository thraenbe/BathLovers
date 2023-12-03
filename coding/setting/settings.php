<head>
<style>
section {
    text-align: center;
    background-color: transparent;
    margin-top: 10px;
}
#userProfile {
    height: 600px;
    width: 600px;
    border-radius: 50%;
	object-fit: cover;    
}
.specs {
    font-size: 20px;
}
.input{
	width: 200px;
	border: none;
	border-bottom: 1px solid #ccc;
	padding: 1px;
	background-color: transparent;
	box-sizing: border-box;		
	margin-bottom: 10px;	
}
.info { 
    margin-bottom: 10px;
}
label{
    font-weight: bold;
}
#changePasswordBtn{
    border: 1px solid #ccc;
    border-radius: 10px;
    width:15%;
    height: 10%;
    background-color: #f8f9fa    
}
</style>
</head>
<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Settings');
?>
<!-- <link  rel="stylesheet" href="/style_setting.css"> -->
<section>
    <?php
    $state ="";
    if (isset($_SESSION['user'])){        
        $username = $_SESSION['user'];
        $sql = "SELECT * FROM student WHERE user_name = '$username'";
        $result = pg_query($dbconn,$sql);
        if (!$result){
            exit;
        } else {
            $row = pg_fetch_assoc($result);  
            $year = $row['year'];
            $specialization = $row['specialization'];
        }
    ?>
    <img id="userProfile" src="ProfileW.jpg" alt="User Profile Photo">
    <!-- User Name -->    
    <div id="userName"><h2><?php echo "$username" ?></h2></div>
    <div class = "info">
    <div class = "specs"><?php echo "specialization: $specialization"?></div>
    <div class = "specs"><?php echo "graduation year: $year"?></div>
    <div class = "specs"><?php echo "$username@uniba.com" ?></div>    
    </div>
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
?>
</section>
<?php include('../templates/footer.php'); ?>    