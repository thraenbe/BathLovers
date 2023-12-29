<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
.day_switch{
    text-align: center;
}
.free_time{
    padding-top: 5%;
    padding-bottom: 5%;
    text-align: center;
}
</style>
</head>
<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Day');
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
        $sum_events = 0;        
        foreach ($all_events as $event){
            $write_day = 0;
            if ($event['event_type']==1){            
                $sum_events++;
                get_event_table($dbconn, $event,$write_day);
            } else {
                if (check_free_days($date_range[$actual_day])){
                    $sum_events++;
                    get_class_table($dbconn, $event,$write_day); 
                }                                
            }        
        }   
        if ($sum_events>0) {
        echo" <input name='remove' type='submit', value='Remove selected'>";
        } else {
            echo "<div class='free_time'>No events you've got free time :-D</div>";
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
    echo "<img src='../images/ComeniusUniversity.png' alt='University'>";
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p>";
}

include('../templates/footer.php');
?>
