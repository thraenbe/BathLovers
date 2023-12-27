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
    echo "<p> Succesfully added  event to schedule </p>";
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
            'nonsch_event_name' => $nonsch_event['event_name'],            
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

function get_classes_table($dbconn, $classes) {
    echo "<h2>Courses:</h2>";
    ?>
    <table class="table table-hover">
        <tbody>
    <?php    
    foreach ($classes as $class){
    ?>
    <div class="card">
    <?php 
            $date_start = explode(" ",$class['time_start']);
            $date_end = explode(" ",$class['time_end']);
            ?>
        <tr>        
            <td><div class="day"> <?php echo $date_start[0]?></div></td>
            <td>
                <button type="button" class="btn btn-info btn-lg btn-block w-100"  >                                                            
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
                            <td> <label for="course<?php echo $class['subject_id']; ?>"> Remove? </td>
                            <td>  <a href= <?php echo $class['information_plan'] ?>>  Information plan</a></td>                            
                        </tr>
                        <tr>
                            <td><input type="checkbox" id ="course<?php echo $class['subject_id']; ?>" name="class[]" value="<?php echo $class['subject_id']; ?>"></td>
                        </tr>                        
                        </tbody>
                    </table>
                </a>
            </td>
        </tr>
    </div>
    <?php
    } ?>
        </tbody>
    </table>
<?php
}


function get_events_table($dbconn, $non_school_events ) {
    echo "<h2>Nonschool events:</h2>";
    ?>
    <table class="table table-hover">
    <tbody>
    <?php    
    foreach ($non_school_events as $event){
        ?>
        <div class="card" >
        <?php 
                $date_start = explode(" ",$event['time_start']);
                $date_end = explode(" ",$event['time_end']);
                $date = explode("-",$date_start[0]);

                ?>
            <tr>
                <td><div class="day"> <?php echo $date[2].".".$date[1].".".$date[0]?></div></td>
                <td>
                    <a type="button" class="btn btn-info btn-lg btn-block w-100"  >

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
                                <td> <label for="<?php echo $event['id'];?>"> Remove? </label></td>
                        </tr>
                            <tr>
                            <td><input type="checkbox" name="nonevent[]" value="<?php echo $event['id']?>" id="<?php echo $event['id']?>"></td>       
                            </tr>
                            </tbody>
                        </table>
                    </a>
                </td>
            </tr>
        </div>
        <?php
    } 
    ?>
    </tbody>
   </table>
    <?php

}


?>