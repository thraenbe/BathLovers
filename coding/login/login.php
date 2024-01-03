<style>
.logo, .formular{
  text-align: center;
}
.fields{
  padding-bottom: 10px;
}
input{
  border-radius: 10px;
}
label{
  font-weight: bold;
}
p{
  text-align: center;
}
</style>
<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Login');
?>

<section>
    <div class="logo"><img src="../images/ComeniusUniversity.png" alt="University"></div>

<?php
if (isset($_POST['username']) && isset ( $_POST['password']) && 
    $user = check_login_credintials($dbconn, $_POST['username'], $_POST['password'])) {
        $_SESSION['user'] = $user['user_name'];
        $_SESSION['specialization'] = $user['specialization'];
        $_SESSION['year'] = $user['year'];    
}
elseif (isset($_POST['logout'])) { 
	session_unset();
	session_destroy();
}

if(isset($_SESSION['user'])) {
?>
<p>Welcome <strong><?php echo $_SESSION['user'] ?></strong>.</p>
<p>  You can view your  <a href="../calendar/daily.php"> daily </a> 
or <a href="../calendar/weekly.php"> weekly </a> schedules. </p>
<p> To create your schedule you can <a href="../search/search.php"> search for classes </a> or
<a href="../calendar/add_to_schedule.php"> Add extra-curricular activities </a>. </p>
<p>  To change your password, visit <a href="../setting/settings.php"> settings </a>. </p>
<form method="post"> 
  <p> 
    <input name="logout" type="submit" id="odhlas" value="Logout"> 
  </p> 
</form>

<?php
} else {
?>
    <form method="post" class="formular">
      <div class="fields">
        <label for="username"> Username </label>
        <input name="username" type="text" size="30" maxlenght="30" id="username" 
        value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>"><br>
      </div>        
      <div class="fields">
        <label for="password">Password </label>
        <input name="password" type="password" size="30" maxlenght="30" id="password">
      </div>
      <p>
			<input name="submit" type="submit" id="submit" value="Login">
		</p>
	</form>
<?php

}


?>
</section>

<?php
include('../templates/footer.php');
?>


