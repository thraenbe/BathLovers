<head><style>
.add-btn{
    background-color: #00BFFF;
    border: 1px solid black;
    border-radius: 10px;
}
</style></head>
<section>
<!-- <img src="../images/ComeniusUniversity.png" alt="University"> -->
<?php
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $user_id = get_user_id($dbconn,$username);
    $classes = get_registred_classes($dbconn,$user_id);    
    $generated_weeks = generateWeeks('2023-05-29','2024-09-01');      
    $num_weeks = sizeof($generated_weeks);
    $actual_week = isset($_SESSION['actual_week']) ? $_SESSION['actual_week'] : (int) $num_weeks/2;    
    $non_school_events = get_nonschool_events($dbconn,$user_id,$generated_weeks[$actual_week]['startDate'],$generated_weeks[$actual_week]['endDate']);
    // chronologically sorted on date interval you type    
    $all_events = sort_all_events($classes,$non_school_events);    
    // stayed old showing events
  
    echo "<form method='post'>";
    ?>
    <input type="hidden" name="actual_week" value="<?php echo $actual_week; ?>">    
    <input type="submit" name="left_click" value="<">
    <div>
        <?php
        $start = explode("-",$generated_weeks[$actual_week]['startDate']); 
        $end = explode("-",$generated_weeks[$actual_week]['endDate']);
        echo "Week $start[2].$start[1].$start[0] - $end[2].$end[1].$end[0]";
        ?>
    </div>    
    <input type="submit" name="right_click" value=">">
    <?php
    get_classes_table($dbconn, $classes); 
    get_events_table($dbconn, $non_school_events);
    if (sizeof($classes) > 0 || sizeof($non_school_events) > 0) {
       echo" <input name='remove' type='submit', value='Remove selected'>";
    } 
    echo "</form>";

    if(isset($_POST["remove"] )) {
        if (isset($_POST['class'])){
            foreach ($_POST['class'] as $class) {
                delete_course($dbconn, $class, $user_id);
                echo "Succesfully deleted '$class'";
            }
            echo "<meta http-equiv='refresh' content='0'>";
        }        
        if (isset($_POST['nonevent'])){
            foreach ($_POST['nonevent'] as $event) {        
                delete_nonschool_event($dbconn, $event);
                echo "Succesfully deleted '$event'";
            }    
            echo "<meta http-equiv='refresh' content='0'>";
            echo "Succesfully deleted selected items";
        }
    } else if (isset($_POST['left_click'])){
        $actual_week = $_POST['actual_week'];    
        $actual_week--;    
        if ($actual_week == -1) {
            $actual_week = $num_weeks-1;
        }    
        $_SESSION['actual_week'] = $actual_week;
        echo "<meta http-equiv='refresh' content='0'>";
    } else if (isset($_POST['right_click'])){
        $actual_week = $_POST['actual_week'];    
        $actual_week++;    
        if ($actual_week == $num_weeks) {
            $actual_week = 0;
        }    
        $_SESSION['actual_week'] = $actual_week;
        echo "<meta http-equiv='refresh' content='0'>";
    }
      
}
else {
    echo "<img src='../images/ComeniusUniversity.png' alt='University'>";
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p>";
}
?>
</section>