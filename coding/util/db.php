<?php
// $dbconn = pg_connect("host=89.221.219.125 dbname=candle user=postgres password=HkqHsep2GQNT"); 
$dbconn = pg_connect("host=localhost dbname=candle user=postgres password=abc-123"); 
if(!$dbconn) {    
    echo "<p> Coulud not connect to database". pg_last_error()."</p>";
}
?>