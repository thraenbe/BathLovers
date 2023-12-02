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
function course_added($dbconn,$subject_id){  
    $sql = "SELECT CASE WHEN EXISTS (
        SELECT 1
        FROM registred_class
        WHERE subject_id = $subject_id
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
function add_course ($dbconn,$subject_id,$user_name) {                
    $user_id = get_user_id($dbconn,$user_name);
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
    $sql = "SELECT id FROM student where user_name = '$user_name'";
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

?>