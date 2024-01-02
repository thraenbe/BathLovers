<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Weekly schedule');
include('schedule_mockup.php');
include('../templates/footer.php');
?>