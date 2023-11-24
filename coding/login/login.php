<?php
session_start();
include('../templates/header.php');
get_header('login');
?>

<section>
    <img src="../images/ComeniusUniversity.png" alt="University">

<?php
if(isset($_SESSION['user'])) {
?>
<p>Welcome <strong><?php echo $_SESSION['username'] ?></strong>.</p>
<form method="post"> 
  <p> 
    <input name="logout" type="submit" id="odhlas" value="Logout"> 
  </p> 
</form>
<?php
} else {
?>
    <form method="post">
        <label for="username"> username </label>
        <input name="username" type="text" size="30" maxlenght="30" id="username" 
        value="<?php if(isset($_POST['username'])) echo $_POST['username']; ?>"><br>
        <label for="password">password </label>
        <input name="password" type="password" size="30" maxlenght="30" id="password">
        <p>
			<input name="submit" type="submit" id="submit" value="Login">
		</p>
	</form>
<?php
    if(isset($_POST['submit'])) {
        echo "This will work in the future!!!!!";
    }

}


?>
</section>

<?php
include('../templates/footer.php');
?>


