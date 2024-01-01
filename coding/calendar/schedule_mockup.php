<head><style>
.week_switch{
    text-align: center;
    padding-bottom: 10px;
}
.free_time{
    padding-top: 5%;
    padding-bottom: 5%;
    text-align: center;
}
.remove_butt{    
    border-radius: 5px;
    background-color: greenyellow;
    font-size: larger;
    font-weight: bolder;
    margin-top: 2%;
}
.remove_div{    
    text-align: center;
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
    <div class="week_switch">
    <input type="submit" name="left_click" value="<">        
    <?php
    $start = explode("-",$generated_weeks[$actual_week]['startDate']); 
    $end = explode("-",$generated_weeks[$actual_week]['endDate']);
    echo "Week $start[2].$start[1].$start[0] - $end[2].$end[1].$end[0]";
    ?>    
    <input type="submit" name="right_click" value=">">
    </div>
    <?php
    $written_days = [];        
    $sum_events = 0; 
    foreach ($all_events as $event){
        $write_day = 0;      
        if ($event['event_type']==1){
            $sum_events++;
            if (!in_array(explode(" ",$event['time_start'])[0],$written_days)){                                          
                $write_day = 1;
                $written_days[]=explode(" ",$event['time_start'])[0];
            }
            foreach($generated_weeks[$actual_week]['datesInWeek'] as $week){
                $i = new DateTime($week);                                                
                if ($i->format('l') == explode(" ",$event['time_start'])[0]) {
                    get_event_table($dbconn, $event,$write_day,$week);             
                    break;
                }
            }            
        } else {
            foreach($generated_weeks[$actual_week]['datesInWeek'] as $week){
                $i = new DateTime($week);                                
                if ($i->format('l') == explode(" ",$event['time_start'])[0]) {
                    if (check_free_days($week)){
                        $sum_events++;
                        if (!in_array(explode(" ",$event['time_start'])[0],$written_days)){                                          
                            $write_day = 1;
                            $written_days[]=explode(" ",$event['time_start'])[0];
                        }        
                        get_class_table($dbconn, $event,$write_day);              
                    }                    
                    break;
                }
            }                        
        }        
    }    
    if ($sum_events>0) {
       echo "<div class='remove_div'>";
       echo "<input class ='remove_butt' name='remove' type='submit', value='Remove selected'>";
       echo "<p> <a href='../courses/roomplan.php'> Room plan </a> </p>";
       echo "</div>";

    } else {
        echo "<div class='free_time'>";
        echo "<p> No events you've got free time :-D </p>";
        echo "<p> You can <a href='../calendar/add_to_schedule.php'>  add extra-curricular activities </a> 
        to your schedule. </p>";
        echo "</div>";
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