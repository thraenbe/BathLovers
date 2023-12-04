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
    $non_school_events = get_nonschool_events($dbconn,$user_id);
    echo "<h2>Courses:</h2>";
    ?>
    <form id="calendarForm" method="post" action="weekly.php">  
        
    <table class="table table-hover">
        <tbody>
    <?php    
    foreach ($classes as $class){
    ?>
    <div class="card" >
    <?php 
            $date_start = explode(" ",$class['time_start']);
            $date_end = explode(" ",$class['time_end']);
            ?>
        <tr>        
            <td><div class="day"> <?php echo $date_start[0]?></div></td>
            <td>
                <a type="button" class="btn btn-info btn-lg btn-block w-100" href= <?php echo $class['information_plan'] ?> >                                                            
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
                            <td><input type="submit" name="course<?php echo $class['subject_id']; ?>" class="add-btn" value="Remove course" data-id="<?php echo $class['user_name']; ?>"></td>
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
    echo "<h2>Nonschool events:</h2>";
    foreach ($non_school_events as $event){
?>
<div class="card">
    <div class="title"><h3><?php echo $event['event_name']?></h3></div>
    <?php 
        $date_start = explode(" ",$event['time_start']);
        $date_end = explode(" ",$event['time_end']);
        $date = explode("-",$date_start[0]);
    ?>
    <div class="day">Date: <?php echo $date[2].".".$date[1].".".$date[0]?></div>
    <div class="start">Start: <?php echo $date_start[1] ?></div>
    <div class="end">End: <?php echo $date_end[1] ?></div>
    <div class="category">Category: <?php echo $event['category'] ?></div>            
    <div class="details">Details: <?php echo $event['details'] ?></div>
    <input type="submit" name="nonevent<?php echo $event['user_name']. " ". $event["time_start"]; ?>" class="add-btn" value="Remove event" data-id="<?php echo $event['user_name']; ?>">
</div>
<?php
    }
    for($i=0;$i<10;$i++){
        $button = "course".$i;
        if (isset($_POST[$button])){            
            delete_course($dbconn,$i,$user_id);                                                   
            break;                
        }                                
    }
?>
</form>
<?php
}
else {
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p>";

}
?>
</section>