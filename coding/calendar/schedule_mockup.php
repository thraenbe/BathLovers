<head><style>
.add-btn{
    background-color: #00BFFF;
    border: 1px solid black;
    border-radius: 10px;
}
</style></head>
<section>
<!-- <img src="../images/ComeniusUniversity.png" alt="University"> -->
<?php
if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $user_id = get_user_id($dbconn,$username);
    $classes = get_registred_classes($dbconn,$user_id);
    $non_school_events = get_nonschool_events($dbconn,$user_id,'2023-12-01','2024-01-24');
    $generated_weeks = generateWeeks('2023-05-29','2024-09-01');      
    // chronologically sorted on date interval you type    
    $all_events = sort_all_events($classes,$non_school_events);    
    // stayed old showing events
  
?>    
    <form method="post">      
    <?php    
    get_classes_table($dbconn, $classes); 
    get_events_table($dbconn, $non_school_events); 
?>
    <input name="remove" type="submit", value="Remove selected">
</form>
<?php
    if(isset($_POST["remove"] ) && isset($_POST['class'])) {
        foreach ($_POST['class'] as $class) {
            delete_course($dbconn, $class, $user_id);
            echo "Succesfully deleted '$class'";
        }
        echo "<meta http-equiv='refresh' content='0'>";
    } 
    if(isset($_POST["remove"] ) && isset($_POST['nonevent'])) {
        foreach ($_POST['nonevent'] as $event) {        
            delete_nonschool_event($dbconn, $event);
            echo "Succesfully deleted '$event'";
        }

        echo "<meta http-equiv='refresh' content='0'>";
        echo "Succesfully deleted selected items";

    }     
}
else {
    echo "<img src='../images/ComeniusUniversity.png' alt='University'>";
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p>";

}
?>
</section>