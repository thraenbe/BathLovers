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
?>