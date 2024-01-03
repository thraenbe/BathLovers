<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.day_switch{
    text-align: center;
    padding-bottom: 10px;
}
.free_time{
    padding-top: 5%;
    padding-bottom: 5%;
    text-align: center;
}
.free_time_link{
    text-align: center;
}
.remove_butt{    
    border-radius: 5px;
    background-color: greenyellow;
    font-size: larger;
    font-weight: bolder;
    margin-top: 2%;
}
.remove_div, .logo{    
    text-align: center;
}
.type_of_day{
    text-align: center;
    font-size: larger;
    font-weight: bold;
}
</style>
</head>
<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Daily schedule');
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $user_id = get_user_id($dbconn,$username);
    $date_range = generateDays('2023-05-29', '2024-09-01');
    $num_days = sizeof($date_range);
    $actual_day = isset($_SESSION['actual_day']) ? $_SESSION['actual_day'] : (int) $num_days/2;
    $day_in_week = new DateTime($date_range[$actual_day]);
    $day_in_week = $day_in_week->format('l');
    $classes = get_registred_class_by_day($dbconn,$user_id,$day_in_week);
    $non_school_events = get_nonschool_events($dbconn,$user_id,$date_range[$actual_day],$date_range[$actual_day]);
    // chronologically sorted on date interval you type    
    $all_events = sort_all_events($classes,$non_school_events); 
    ?>
    <form method="post">
        <input type="hidden" name="actual_day" value="<?php echo $actual_day; ?>">    
        <div class="day_switch">
        <input type="submit" name="left_click" value="<">
        <?php
        $date_array = explode("-",$date_range[$actual_day]);        
        echo "$day_in_week $date_array[2].$date_array[1].$date_array[0]"; ?>
        <input type="submit" name="right_click" value=">">
        </div>
        <?php
        $type_of_day1 = type_of_day(new DateTime($date_range[$actual_day]));    
        if ($type_of_day1 == "celebration"){
            echo "<div class='type_of_day'>Celebration</div>";                    
        } else if ($type_of_day1 == "holidays"){
            echo "<div class='type_of_day'>Holidays</div>";
        } else if ($type_of_day1 == "weekend"){
            echo "<div class='type_of_day'>Weekend</div>";
        } else if ($type_of_day1 == "exams"){
            echo "<div class='type_of_day'>Exam season</div>";
        }
        $sum_events = 0;        
        foreach ($all_events as $event){
            $write_day = 0;
            if ($event['event_type']==1){            
                $sum_events++;
                get_event_table($dbconn, $event,$write_day,$date_range[$actual_day]);
            } else {
                if (check_free_days($date_range[$actual_day])){
                    $sum_events++;
                    get_class_table($dbconn, $event,$write_day); 
                }                                
            }        
        }   
        if ($sum_events>0) {
            echo" <div class='remove_div'><input class = 'remove_butt' name='remove' type='submit', value='Remove selected'></div>";
        } else {
            ?>
            <div class='free_time'>
                <p>No events you've got free time :-D</p>
                <p>You can add  <a href="../search/search.php">course</a> to your schedule</p>
                <p>You can add <a href="../calendar/add_to_schedule.php">extra-curricular activities</a> to your schedule.</p>
            </div>
            <?php
        }        
        ?>
    </form>    
    <?php
    // Inside the form handling block
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
    } else if (isset($_POST["left_click"])) {
        $actual_day = $_POST['actual_day'];    
        $actual_day--;    
        if ($actual_day == -1) {
            $actual_day = $num_days-1;
        }    
        $_SESSION['actual_day'] = $actual_day;
        echo "<meta http-equiv='refresh' content='0'>";
    } else if (isset($_POST["right_click"])){
        $actual_day = $_POST['actual_day'];    
        $actual_day++;    
        if ($actual_day == $num_days) {
            $actual_day = 0;
        }    
        $_SESSION['actual_day'] = $actual_day;
        echo "<meta http-equiv='refresh' content='0'>";
    }
} else {
    echo "<div class='logo'><img src='../images/ComeniusUniversity.png' alt='University'>";
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p></div>";
}
include('../templates/footer.php');
?>
