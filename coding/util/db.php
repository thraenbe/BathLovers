<?php
$dbconn = pg_connect("host=89.221.219.125 dbname=candle user=postgres password=HkqHsep2GQNT"); 
if(!$dbconn) {
    echo "scojcos";
    echo "<p> Coulud not connect to database". pg_last_error()."</p>";
}


?>