<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Login');
?>

<section>
    <img src="../images/ComeniusUniversity.png" alt="University">

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
<form method="post"> 
  <p> 
    <input name="logout" type="submit" id="odhlas" value="Logout"> 
  </p> 
</form>
<?php
} else {
?>
    <form method="post">
        <label for="username"> Username </label>
        <input name="username" type="text" size="30" maxlenght="30" id="username" 
        value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>"><br>
        <label for="password">Password </label>
        <input name="password" type="password" size="30" maxlenght="30" id="password">
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


