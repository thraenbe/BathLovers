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
$state ="";
if (isset($_SESSION['user'])){        
    $username = $_SESSION['user'];
?>
<!-- <header>Finding</header> -->
<form method="post">
    <input  id="course" name="course" class="input" placeholder="Search...">
    <input id="Search" type="submit" name="Search" value="Search" >        
<?php
if(isset($_POST['Search'])){
    $searchtext = $_POST['course'];
    $sql = "SELECT * FROM subjects WHERE lower(name_en) LIKE lower('%$searchtext%') OR lower(teacher) LIKE lower('%$searchtext%')";    
    $result = pg_query($dbconn,$sql);
    if (!$result){
        exit;
    } else {
        $number = 0;        
        while ($row = pg_fetch_assoc($result)) {            
?>
<div class="card">
    <div class="title"><?php echo $row['name_en']?></div>
    <div class="start"><?php echo $row['time_start']?></div>
    <div class="end"><?php echo $row['time_end']?></div>
    <div class="end">Teacher: <?php echo $row['teacher']?></div>
</div>
<?php
}        
}    
}
?>
</form>
<?php }
?>
</section>
<?php
include('../templates/footer.php');
?>