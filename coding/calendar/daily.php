<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Day');
// include('schedule_mockup.php');
// Použitie príkladu:
$date_range = generateDays('2024-02-28', '2024-03-10');
?>
<form method="post">
    <?php $actual_week = 1;?>
    <input type="submit" name="left_click" value="<">    
    <div><?php echo $date_range[$actual_week]; ?></div>
    <input type="submit" name="right_click" value=">">
</form>
<?php
if (isset($_POST["left_click"])){    
    $actual_week--;    
    if($actual_week==-1){
        $actual_week = 11;
    }    
    echo "<meta http-equiv='refresh' content='0'>";
}
include('../templates/footer.php');
?>