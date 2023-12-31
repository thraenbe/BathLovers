<head>
    <style>
        .card{
            padding-top: 2%;
        }
        .day{            
            padding-top: 10px;
            padding-bottom: 10px;
            font-size: 40px;
            font-weight: bold;
        }
        .title,.time,.remove,.cekbox,.day{
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
date_default_timezone_set('Europe/Bratislava');
function check_login_credintials($dbconn, $username, $password){
    if(!$dbconn) {
        echo "<p> Coulud not connect to database". preg_last_error()."</p>";
        return false;
    }
    $sql = "SELECT * FROM student WHERE user_name='$username' AND password='$password'";
    $result = pg_query($dbconn, $sql);
    if(!$result) {
        echo "<p> Error ". preg_last_error()."</p>";
        return false;
    }
    if (pg_num_rows($result) == 0) {
        return false;
    }
    $row = pg_fetch_assoc($result);
    pg_free_result($result);
    return $row;
}


function search_subjects($dbconn,$findtext){
    $sql = "SELECT * FROM subjects WHERE lower(name_en) LIKE lower('%$findtext%') OR lower(teacher) LIKE lower('%$findtext%')";
    $result = pg_query($dbconn, $sql);
    $search_results = [];
    if (!$result) {
        return [];
    } else {        
        while ($row = pg_fetch_assoc($result)) {
            $search_results[] = $row;
        }
        return $search_results;
    }    
}
function course_added($dbconn,$subject_id,$user_id){  
    $sql = "SELECT CASE WHEN EXISTS (
        SELECT 1
        FROM registred_class
        WHERE subject_id = $subject_id AND user_name = $user_id
    )
    THEN CAST(1 AS BIT)
    ELSE CAST(0 AS BIT)
    END AS is_object_registered";
    $result = pg_query($dbconn, $sql);
    if (!$result) {
        return false;
    } else {        
        $row = pg_fetch_assoc($result);
        $condition = (bool) $row['is_object_registered'];
        return $condition;
    }
}
function add_course ($dbconn,$subject_id,$user_id) {                    
    $sql = "INSERT INTO registred_class (user_name, subject_id, rooms)    
    VALUES ($user_id,$subject_id,(
        SELECT id from rooms
    where id not in (SELECT rooms from registred_class)
    ORDER BY random()
    LIMIT 1
        ));";
    $result = pg_query($dbconn, $sql);
    if (!$result){
        echo "Unsucessfull adding";
    }
}

function insert_other_event($dbconn, $user_name, $event_name, 
    $time_start, $time_end, $tag,  $description) {
    if(!$dbconn) {
        echo "<p> Coulud not connect to database". preg_last_error()."</p>";
        exit;
    }
    
    $event_name = pg_escape_string($event_name);
    $time_start = pg_escape_string($time_start);
    $time_end = pg_escape_string($time_end);
    $tag = pg_escape_string($tag);
    $description = pg_escape_string($description);
    $sql = "INSERT INTO other_events (user_name, event_name, time_start, time_end, category, details)
    VALUES ('$user_name', '$event_name', '$time_start', '$time_end', '$tag', '$description')";
     $result = pg_query($dbconn, $sql);
     if(!$result) {
        echo "<p> Error ". preg_last_error()."</p>";
        exit;
    }
    echo "<p style='color:green; font-weight:bold'> Succesfully added  event to schedule </p>";
}
function get_user_id($dbconn,$user_name) {
    $sql = "SELECT id FROM student WHERE user_name = '$user_name'";
    $result = pg_query($dbconn, $sql);
    if (!$result) {
        echo "
        ". preg_last_error()."";
        exit;
    } else { 
        $row = pg_fetch_array($result);
        return $row["id"];  
    }
}
function validate_input($input) {
    return trim(strip_tags($input));
}    
function get_Subject($dbconn,$id){
    $sql = "SELECT * FROM subjects WHERE id = '$id'";
    $result = pg_query($dbconn, $sql);
    if (!$result) {
        echo "
        ". preg_last_error()."";
        exit;
    } else { 
        $row = pg_fetch_array($result);
        return $row;
    }
}
// list of classes and nonschool events                    
function get_registred_classes($dbconn,$user_id){    
    $sql = "SELECT * FROM registred_class rc 
            JOIN subjects s ON rc.subject_id = s.id 
            WHERE rc.user_name = '$user_id';";
    $result = pg_query($dbconn, $sql);
    $search_results = [];
    if (!$result) {
        return [];
    } else {
        while ($row = pg_fetch_assoc($result)) {
            $search_results[] = $row;
        }
        usort($search_results, function ($a, $b) {
            $daysOfWeek = [
                'Monday' => 1,
                'Tuesday' => 2,
                'Wednesday' => 3,
                'Thursday' => 4,
                'Friday' => 5,
                'Saturday' => 6,
                'Sunday' => 7
            ];
            $dayOfWeekA = $daysOfWeek[explode(' ', $a['time_start'])[0]];
            $dayOfWeekB = $daysOfWeek[explode(' ', $b['time_start'])[0]];
            $timeA = strtotime($a['time_start']);
            $timeB = strtotime($b['time_start']);
            if ($dayOfWeekA != $dayOfWeekB) {
                return $dayOfWeekA - $dayOfWeekB;
            }
            return $timeA - $timeB;
        });
        return $search_results;
    }
}
function get_registred_class_by_day($dbconn,$user_id,$day){
    $sql = "SELECT * FROM registred_class rc
    JOIN subjects s ON rc.subject_id = s.id
    WHERE rc.user_name = '$user_id' AND time_start LIKE '$day%'";
    $result = pg_query($dbconn, $sql);
    $search_results = [];
    if (!$result) {
        return [];
    } else {
        while ($row = pg_fetch_assoc($result)) {
            $search_results[] = $row;
        }
        return $search_results;
    }
}
function get_nonschool_events($dbconn, $user_id,$start_date_week,$end_date_week)
{
    $sql = "SELECT * FROM other_events WHERE user_name = $user_id AND time_start >= '$start_date_week 00:00' and time_end <='$end_date_week 23:59';";
    $result = pg_query($dbconn, $sql);
    $search_results = [];

    if (!$result) {
        return [];
    } else {
        while ($row = pg_fetch_assoc($result)) {
            $search_results[] = $row;
        }

        // Sort events by start_date
        usort($search_results, function ($a, $b) {
            $start_date_a = strtotime($a['time_start']);
            $start_date_b = strtotime($b['time_start']);

            return $start_date_a - $start_date_b;
        });

        return $search_results;
    }
}
function sort_all_events($classes,$nonschool_events){
    $converted_nonsch_events = convertToClassFormat($nonschool_events);
    $all_events = [];
    foreach ($classes as $class){
        $all_events[] = 
        [
            'event_type' => '0',
            'user_name' => $class['user_name'], 
            'subject_id' => $class['subject_id'], 
            'rooms' => $class['rooms'], 
            'id' => $class['id'], 
            'name' => $class['name'], 
            'name_en' => $class['name_en'], 
            'teacher' => $class['teacher'], 
            'information_plan' => $class['information_plan'],
            'time_start' => $class['time_start'],
            'time_end' => $class['time_end'],
        ];
    }
    foreach($converted_nonsch_events as $nonsch_event){
        $all_events[] = 
        [
            'event_type' => '1',
            'id' => $nonsch_event['id'],
            'user_name' => $nonsch_event['user_name'],
            'event_name' => $nonsch_event['event_name'],            
            'time_start' => $nonsch_event['time_start'],
            'time_end' => $nonsch_event['time_end'],
            'category' => $nonsch_event['category'],
            'details' => $nonsch_event['details']
        ];
    }
    usort($all_events, function ($a, $b) {
        $daysOfWeek = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];        
        $dayOfWeekA = $daysOfWeek[explode(' ', $a['time_start'])[0]];
        $dayOfWeekB = $daysOfWeek[explode(' ', $b['time_start'])[0]];
        $timeA = strtotime($a['time_start']);
        $timeB = strtotime($b['time_start']);
        if ($dayOfWeekA != $dayOfWeekB) {
            return $dayOfWeekA - $dayOfWeekB;
        }
        return $timeA - $timeB;
    });    
    return $all_events;
}
// delete existing courses and nonschool events
function delete_course($dbconn,$subject_id,$user_id){
    echo $subject_id;
    $sql = "DELETE FROM registred_class WHERE subject_id = $subject_id AND user_name = $user_id";
    $result = pg_query($dbconn, $sql);
    if (!$result) {
        echo "
        ". preg_last_error()."";
        exit;
    } else {
        pg_free_result($result);
    }
}

function delete_nonschool_event($dbconn,$event_id){
    $sql = "DELETE FROM other_events WHERE id = $event_id";
    $result = pg_query($dbconn, $sql);
    if (!$result) {
        echo "
        ". preg_last_error()."";
        exit;
    } else {
        pg_free_result($result);
    }
}

function get_room_name ($dbconn,$roomid){
    $sql = "SELECT name FROM rooms where id = '$roomid'";
    $result = pg_query($dbconn, $sql);
    if (!$result) {
        echo "
        ". preg_last_error()."";
        exit;
    } else { 
        $row = pg_fetch_array($result);
        return $row["name"];  
    }    
}

function get_recomended_subjects ($dbconn,$user_id){
    $sql = "SELECT * FROM recomended_subjects r_s
    JOIN subjects s on r_s.subject_id = s.id
    WHERE r_s.user_id = '$user_id';";
    $result = pg_query($dbconn, $sql);
    $search_results = [];
    if (!$result) {
        return [];
    } else {
        while ($row = pg_fetch_assoc($result)) {
            $search_results[] = $row;
        }
        return $search_results;
    }
}
// resolving conflicts part iteration through all events 
// !!!!!!!events are type of class
function hasTimeConflict_Class_Class($newEvent, $existingEvents) {
    foreach ($existingEvents as $existingEvent) {           
        if (checkTimeConflict_Class_Class($newEvent, $existingEvent)) {                        
            return true; // Conflict detected
        }
    }
    return false; // No conflict
}
// resolving conflicts part compare event vs event
function checkTimeConflict_Class_Class($event1, $event2) {
    $day_week = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $s1_day_h_m = explode(" ",$event1['time_start']);
    $s1_h_m = explode(":",$s1_day_h_m[1]);
    $start1_day = array_search($s1_day_h_m[0],$day_week);
    $start1_hour = (int) $s1_h_m[0];
    $start1_minute = (int) $s1_h_m[1];
    
    $s2_day_h_m = explode(" ",$event2['time_start']);
    $s2_h_m = explode(":",$s2_day_h_m[1]);
    $start2_day = array_search($s2_day_h_m[0],$day_week);
    $start2_hour = (int) $s2_h_m[0];
    $start2_minute = (int) $s2_h_m[1];
    
    $e1_day_h_m = explode(" ",$event1['time_end']);
    $e1_h_m = explode(":",$e1_day_h_m[1]);
    $end1_day = array_search($e1_day_h_m[0],$day_week);
    $end1_hour = (int) $e1_h_m[0];
    $end1_minute = (int) $e1_h_m[1];
    
    $e2_day_h_m = explode(" ",$event2['time_end']);
    $e2_h_m = explode(":",$e2_day_h_m[1]);
    $end2_day = array_search($e2_day_h_m[0],$day_week);
    $end2_hour = (int) $e2_h_m[0];
    $end2_minute = (int) $e2_h_m[1];
    
    $s1_seconds = $start1_day*24*60 + $start1_hour*60 + $start1_minute;
    $s2_seconds = $start2_day*24*60 + $start2_hour*60 + $start2_minute;
    $e1_seconds = $end1_day*24*60 + $end1_hour*60 + $end1_minute;
    $e2_seconds = $end2_day*24*60 + $end2_hour*60 + $end2_minute;
    if($s1_seconds == $s2_seconds || $e1_seconds == $e2_seconds) return true;
    if($s1_seconds < $s2_seconds){
        if ($e1_seconds > $s2_seconds)return true;
    }
    if($s1_seconds > $s2_seconds){
        if ($e2_seconds > $s1_seconds)return true;
    }
    return false;  
    
}  
// convert nonschool event to class format
function convertToClassFormat($existingNonSchoolEvents) {    
    $nontoclass = [];
    foreach ($existingNonSchoolEvents as $event) {        
        $startDateTime = new DateTime($event['time_start']);
        $endDateTime = new DateTime($event['time_end']);                        
        $nontoclass[] = [
            'id' => $event['id'],
            'user_name' => $event['user_name'],
            'event_name' => $event['event_name'],            
            'time_start' => $startDateTime->format('l')." ".explode(' ',$event['time_start'])[1],
            'time_end' => $endDateTime->format('l')." ".explode(' ',$event['time_end'])[1],
            'category' => $event['category'],
            'details' => $event['details']
        ];            
    }    
    return $nontoclass;
}
// time ocnflict nonschool event vs nonschool event
function check_time_conflict_non_school_event_vs_nonschool_event($new_non_sch_event,$existing_nonsch_events){
    $n_start_date = new DateTime($new_non_sch_event['time_start']);
    $n_end_date = new DateTime($new_non_sch_event['time_end']);
    foreach($existing_nonsch_events as $exist_nonsch_event){
        $e_start_date = new DateTime($exist_nonsch_event['time_start']);
        $e_end_date = new DateTime($exist_nonsch_event['time_end']);        
        if($n_start_date == $e_start_date || $n_end_date == $e_end_date) return true;
        if($n_start_date < $e_start_date){
            if ($n_end_date > $e_start_date){
                return true;
            }
        }
        if($n_start_date > $e_end_date){
            if ($e_end_date > $n_start_date){
                return true;
            }
        }        
    }
    return false;
}
function type_of_day($date){
    $exam_moments = [new DateTime("2023-05-29"),new DateTime("2023-06-30"),new DateTime("2024-01-02"),new DateTime("2024-02-16"),new DateTime("2024-05-19"),new DateTime("2024-06-30")];
    $holidays = [new DateTime("2023-07-01"),new DateTime("2023-09-17"),new DateTime("2024-07-01"),new DateTime("2024-09-17")];
    $freedays = [new DateTime("2023-07-05"),new DateTime("2023-08-29"),new DateTime("2023-09-01"),new DateTime("2023-09-15"),new DateTime("2023-11-01"),new DateTime("2023-12-24"),new DateTime("2023-12-25"),new DateTime("2023-12-26"),new DateTime("2024-01-01"),new DateTime("2024-01-06"),new DateTime("2024-03-29"),new DateTime("2024-04-01"),new DateTime("2024-05-01"),new DateTime("2024-05-08"),new DateTime("2024-07-05"),new DateTime("2024-08-29"),new DateTime("2024-09-01"),new DateTime("2024-09-15")];    
    if (in_array($date,$freedays)){
        return "celebration";
    } else if (($date>=$holidays[0] && $date<=$holidays[1]) || ($date>=$holidays[2] && $date<=$holidays[3])){
        return "holidays";
    } else if (($date>=$exam_moments[0] && $date<=$exam_moments[1]) || ($date>=$exam_moments[2] && $date<=$exam_moments[3]) || ($date>=$exam_moments[4] && $date<=$exam_moments[5])){
        return "exams";
    } else if ($date->format('l')=="Saturday" or $date->format('l')=="Sunday"){
        return "weekend";
    } else{
        return "nothing";
    }
}
function check_free_days($date){
    $semester = [new DateTime("2023-09-19"),new DateTime("2023-12-15"),new DateTime("2024-02-19"),new DateTime("2024-05-17")];
    $freedays = [new DateTime("2023-07-05"),new DateTime("2023-08-29"),new DateTime("2023-09-01"),new DateTime("2023-09-15"),new DateTime("2023-11-01"),new DateTime("2023-12-24"),new DateTime("2023-12-25"),new DateTime("2023-12-26"),new DateTime("2024-01-01"),new DateTime("2024-01-06"),new DateTime("2024-03-29"),new DateTime("2024-04-01"),new DateTime("2024-05-01"),new DateTime("2024-05-08"),new DateTime("2024-07-05"),new DateTime("2024-08-29"),new DateTime("2024-09-01"),new DateTime("2024-09-15")];
    $date = new DateTime($date);
    if ((($date >= $semester[0] and $date <= $semester[1]) or ($date >= $semester[2] and $date <= $semester[3])) and (!in_array($date,$freedays))){
        return true;
    } else {
        return false;
    }
}
// weekly view generate weeks
function generateWeeks($start_date,$end_date) {
    $weeksArray = array();
    $startDate = new DateTime($start_date);
    $endDate = new DateTime($end_date);    
    $currentDate = clone $startDate;
    $currentWeek = 1;

    while ($currentDate <= $endDate) {
        $weekStartDate = clone $currentDate;
        $weekEndDate = clone $currentDate;
        $weekEndDate->modify('+6 days');

        $datesInWeek = array();
        $currentDay = clone $weekStartDate;
        while ($currentDay <= $weekEndDate) {
            $datesInWeek[] = $currentDay->format('Y-m-d');
            $currentDay->modify('+1 day');
        }

        $weeksArray[] = array(
            'week' => $currentWeek,
            'startDate' => $weekStartDate->format('Y-m-d'),
            'endDate' => $weekEndDate->format('Y-m-d'),
            'datesInWeek' => $datesInWeek
        );

        $currentDate->modify('+7 days');
        $currentWeek++;
    }
    return $weeksArray;
}
function generateDays($start_date, $end_date) {
    $date_range = array();

    $current_date = new DateTime($start_date);
    $end_date = new DateTime($end_date);

    while ($current_date <= $end_date) {
        $date_range[] = $current_date->format('Y-m-d');
        $current_date->modify('+1 day');
    }

    return $date_range;
}
// weekly view
function get_class_table($dbconn, $class,$write_day) {                
    ?>
    <div class="card" id="course">
    <?php 
        $date_start = explode(" ",$class['time_start']);
        $date_end = explode(" ",$class['time_end']);
        ?>
        <tr>        
            <td>                
                <?php                 
                if($write_day==1){                    
                    echo "<div class='day'>$date_start[0]</div>";                   
                } 
                ?>                
            </td>
            <td>
                <button type="button" class="btn btn-info btn-lg btn-block w-100" style="background-color: #CFECFF;">
                    <table class=" w-100">
                        <tbody>
                        <tr>
                            <th><div class="title"><h3><?php echo $class['name_en'] ?></h3></div></th>
                            <td></td>
                        </tr>
                        <tr>
                            <td> <div class="time"><?php echo $date_start[1] ?> - <?php echo $date_end[1] ?> </div> </td>
                            <td> room: <?php echo get_room_name($dbconn,$class['rooms']);?></td>
                        </tr>
                        <tr>
                            <td class="remove"> <label for="course<?php echo $class['subject_id']; ?>"> Remove? </td>
                            <td>  <a href= <?php echo $class['information_plan'] ?> target="_blank">  Information plan (Faculty website)</a></td>                            
                        </tr>
                        <tr>
                            <td class="cekbox"><input type="checkbox" id ="course<?php echo $class['subject_id']; ?>" name="class[]" value="<?php echo $class['subject_id']; ?>"></td>
                        </tr>                        
                        </tbody>
                    </table>
                </button>                
            </td>
        </tr>
    </div>    
<?php
}


function get_event_table($dbconn,$event,$write_day,$date_y_M_D) {    
        ?>
        <div class="card" id="other">
        <?php             
            $date_start = explode(" ",$event['time_start']);
            $date_end = explode(" ",$event['time_end']);
            $date = $date_start[0];            
            ?>
            <tr>
                <td>
                <?php                 
                if($write_day==1){
                    echo "<div class='day'> $date </div>";                       
                    $type_of_day1 = type_of_day(new DateTime($date_y_M_D));                    
                    if ($type_of_day1 == "celebration"){
                        echo "<div class='type_of_day' style='font-size:28px'>celebration</div>";
                    } else if ($type_of_day1 == "holidays"){
                        echo "<div class='type_of_day' style='font-size:28px'>holidays</div>";
                    } else if ($type_of_day1 == "weekend"){
                        echo "<div class='type_of_day' style='font-size:28px'>weekend</div>";
                    } else if ($type_of_day1 == "exams"){
                        echo "<div class='type_of_day' style='font-size:28px'>exam time</div>";
                    }                 
                }   
                ?></td>
                <td>
                    <a type="button" class="btn btn-warning btn-lg btn-block w-100" style="background-color: #FDF1DB;">
                        <table class=" w-100">
                            <tbody>
                            <tr>
                                <th><div class="title"><h3><?php echo $event['event_name'] ?></h3></div></th>
                   
                            </tr>
                            <tr>
                                <td> <div class="time"><?php echo $date_start[1] ?> - <?php echo $date_end[1] ?> </div> </td>
                                <td> <div class="category"> <?php echo $event['category'] ?></div> </td>
                            </tr>
                            <tr>
                                <td class="remove"> <label for="<?php echo $event['id'];?>"> Remove? </label></td>
                        </tr>
                            <tr>
                            <td class="cekbox"><input type="checkbox" name="nonevent[]" value="<?php echo $event['id']?>" id="<?php echo $event['id']?>"></td>       
                            </tr>
                            </tbody>
                        </table>
                    </a>
                </td>
            </tr>
        </div>    
    <?php
}
function write_courses($dbconn,$user_id){
    $rows = search_subjects($dbconn,$_SESSION['searchtext']);                    
    foreach ($rows as $row) {        
        $new_event = get_Subject($dbconn,$row['id']);
        $classes = get_registred_classes($dbconn,$user_id);
        $non_school_events = get_nonschool_events($dbconn,$user_id,'2023-05-01','2024-09-24');
        $non_school_events_all = [];
        foreach ($non_school_events as $nonsch_event){
            if (check_free_days($nonsch_event['time_start'])){
                $non_school_events_all[] = $nonsch_event;
            }
        }        
    ?>
        <div class="center">
            <div class="title"><?php echo $row['name_en'] ?></div>
            <?php
            $start=explode(" ",$row['time_start']);
            $end=explode(" ",$row['time_end']);                        
            ?>
            <div class="day1"><?php echo $start[0] ?></div>
            <div class="start">Start: <?php echo $start[1] ?></div>
            <div class="end">End: <?php echo $end[1] ?></div>
            <div class="teacher">Teacher: <?php echo $row['teacher'] ?></div>
            <div class="course_site"><a class="course_link" href="<?php echo $row['information_plan'];?>" target="_blank">course site</a></div>
            <?php                                     
            $someCondition = course_added($dbconn,$row['id'],$user_id);
            $butt_value = "REGISTER";
            if ($someCondition == 0 and hasTimeConflict_Class_Class($new_event, $classes)) {
                echo "<div class='conflict'>X This course has time conflict with another course</div>";
                $butt_value="REGISTER DESPITE CONFLICT";
            } else if ($someCondition == 0 and hasTimeConflict_Class_Class($new_event,convertToClassFormat($non_school_events_all))){
                echo "<div class='conflict'>X This course has time conflict with another activities</div>";
                $butt_value="REGISTER DESPITE CONFLICT";
            }
            ?>
            <input style="background-color: #8698E9;margin-top:10px;" type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="<?php echo $butt_value;?>" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
        </div> <br>
    <?php
    }
}
?>