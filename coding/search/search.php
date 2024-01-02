<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
section {
text-align: center;
background-color: transparent;
margin-top: 10px;
}
.searchtool{
    margin: 10px;
}
.center{
    border: 3px solid #ccc;
    border-radius: 20px;    
    margin: auto;    
    width: 35%;
    padding: 10px; 
    background-color: #00BFFF;   
}
.add-btn{        
    font-weight: bolt;
    border: 3px solid black;
    border-radius: 10px;
    background-color: #f8f9fa;    
    margin: 10px;
}
.input {
    width: 30%;    
}
.bad_notify, .good_notify {
    position: fixed;
    top: 38%;
    left: 50%;
    transform: translateX(-50%);
    width: 30%;
    border: 3px solid #ccc;
    padding: 1px;
    border-radius: 10px;
}
.bad_notify {
    color: black;
    background-color: red;
}
.good_notify {    
    color: white;
    background-color: green;
}
.recomend{
    font-size: 16;
    font-style: italic;  
    padding-bottom: 10px;  
}
.course_link{
    font-size: 16px;
    color: red;
    font-weight: bold;
}
.course_link:hover{
    font-size: 16px;
    color: blue;
    font-weight: bold;
}
</style>
</head>
<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Search');
?>

<section>
    <img src="../images/ComeniusUniversity.png" alt="University">
    <?php
    $state = "";
    if (isset($_SESSION['user'])) {
        $username = $_SESSION['user'];
        $user_id = get_user_id($dbconn,$username);
    ?>
        <form id="searchForm" method="post">
            <div class="searchtool">
            <input id="course" name="course" class="input" placeholder="Search...">
            <input id="Search" type="submit" name="Search" value="Search">
            </div>                        
            <h3>Recomended_subjects</h3>
            <?php
            foreach (get_recomended_subjects($dbconn,$user_id) as $recom_course){
                ?>
                <div class="recomend"><?php echo "Course: ".$recom_course['name_en']."  on ".$recom_course['time_start']."-".explode(" ",$recom_course['time_end'])[1]?></div>
                <?php                
            }
            if (isset($_POST['Search'])) {
                $searchtext = $_POST['course'];
                $_SESSION['searchtext']=$searchtext;
                $rows = search_subjects($dbconn,$searchtext);                
                foreach ($rows as $row) {
            ?>                    
                    <div class="center">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <?php
                        $start=explode(" ",$row['time_start']);
                        $end=explode(" ",$row['time_end']);                        
                        ?>
                        <div class="day"><?php echo $start[0] ?></div>
                        <div class="start">Start: <?php echo $start[1] ?></div>
                        <div class="end">End: <?php echo $end[1] ?></div>
                        <div class="teacher">Teacher: <?php echo $row['teacher'] ?></div>
                        <div class="course_site"><a class="course_link" href="<?php echo $row['information_plan'];?>" target="_blank">course site</a></div>
                        <?php                         
                        $someCondition = course_added($dbconn,$row['id'],$user_id);
                        ?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="ADD" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                    </div> <br>
            <?php                                        
                }
            }
            for ($i=0;$i<30;$i++){
                $buttonName = 'class' . $i;                        
                if (isset($_POST[$buttonName])) {                    
                    $new_event = get_Subject($dbconn,$i);
                    $classes = get_registred_classes($dbconn,$user_id);
                    $non_school_events = get_nonschool_events($dbconn,$user_id,'2023-05-01','2024-09-24');
                    $non_school_events_all = [];
                    foreach ($non_school_events as $nonsch_event){
                        if (check_free_days($nonsch_event['time_start'])){
                            $non_school_events_all[] = $nonsch_event;
                        }
                    }
                    if (hasTimeConflict_Class_Class($new_event, $classes)) {
                        echo "<p id='notification' class = 'bad_notify'> <strong>× Time conflict detected with classes</strong> </p>";
                    } else if (hasTimeConflict_Class_Class($new_event,convertToClassFormat($non_school_events_all))){
                        echo "<p id='notification' class = 'bad_notify button'> <strong>× Time conflict detected with nonschool event</strong> </p>";
                    } else {
                        add_course($dbconn, $i, $user_id);
                        $n_class_name = get_Subject($dbconn,$i)['name'];
                        echo "<p id='notification' class = 'good_notify'> <strong>✓ Class $n_class_name suscessfullly added to schedule </strong> </p>";
                    }                    
                    $rows = search_subjects($dbconn,$_SESSION['searchtext']);
                    foreach ($rows as $row) {
                    ?>
                    <div class="center">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <div class="start"><?php echo $row['time_start'] ?></div>
                        <div class="end"><?php echo $row['time_end'] ?></div>
                        <div class="teacher">Teacher: <?php echo $row['teacher'] ?></div>
                        <div class="course_site"><a class="course_link" href="<?php echo $row['information_plan'];?>" target="_blank">course site</a></div>
                        <?php $someCondition = course_added($dbconn,$row['id'],$user_id);?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn"  value="ADD" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                    </div> <br>
                    <?php
                    }
                    break;
                }
            }
            ?>
        </form>        
    <?php                     
    }
    else {
        echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </pP";
        
    }
    // ?>
</section>
<?php
include('../templates/footer.php');
?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var notification = document.getElementById('notification');
    document.getElementById('course').focus();
    // Show the notification
    notification.style.display = 'block';

    // Hide the notification after 10 seconds
    setTimeout(function() {
      notification.style.display = 'none';
    }, 3000);
  });
</script>