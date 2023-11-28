<?php
$dbconn = pg_connect("host=localhost dbname=candle user=postgres password=postgres"); 
if(!$dbconn) {
    echo "<p> Coulud not connect to database". preg_last_error()."</p>";
}


?>