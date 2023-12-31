<style>
    .logo, .formular,p,div{
        text-align: center;
    }    
    .fields{
        padding-bottom: 10px;
    }
    label{
        font-weight: bold;
    }
    input,textarea{
        border-radius: 10px;
    }
</style>
<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Add activity to schedule');
?>
<section>
<?php
if (isset($_SESSION['user'])) {
?>
<form method="post" class="formular">
    <div class="fields">
        <label for="eventName"> Event name</label>
        <input name="eventName"type="text" size="30" maxlength="30" id=eventName><br>
    </div>
    <div class="fields">
        <label for="date">Date</label>
        <input type="date" id="date" name="date"> <br>
    </div>
    <div class="fields">
        <label for="start">Start time:</label>
        <input type="time" id="start" name="start"> <br>
    </div>
    <div class="fields">
        <label for="end">End time:</label>
        <input type="time" id="end" name="end"> <br>
    </div>
    <div class="fields">
        <label for="tag"> Tag </label>
        <select name="tag" id="tag">
            <option value="work" selected>Work </option>
            <option value="sport" >Sport </option>
            <option value="dining" >Dining </option>
            <option value="other">Other </option>
        </select> <br>
    </div>
    <div class="fields">
        <label for="description"> Description</label>
        <textarea id="description" placeholder="Enter description..." name="description" rows="2" cols="20">
        </textarea> 
    </div>
    <br>
    <input name="add" type="submit" value="Add to schedule">



</form>
<?php
    if(isset($_POST['add'])) {
        if(isset($_POST['eventName'])) $event_name = validate_input($_POST['eventName']); else $event_name ='';
        if(isset($_POST['date'])) $date = validate_input($_POST['date']); else $date = '';
        if(isset($_POST['start'])) $start_time = validate_input($date . " " .$_POST['start']); else $start_time = '';
        if(isset($_POST['end'])) $end_time = validate_input($date . " ".$_POST['end']); else $end_time ='';
        if(isset($_POST['tag'])) $tag = validate_input($_POST['tag']); else $tag = '';
        if(isset($_POST['description'])) $description = validate_input($_POST['description']); else $description = '';
        $errors = array();
        if(empty($event_name)) $errors['eventName'] = "Empty name";
        if(empty($date)) $errors["date"] =" Empty date";
        if(empty($start_time) || empty($end_time)) $errors["time"] = "Empty time";
        if(empty($tag)) $errors["tag"] = "Empty tag";
        if(empty($description)) $errors["description"] ="Empyty desc";
    }
    if(isset($_POST["add"]) && empty($errors)) {
        $user_id = get_user_id($dbconn, $_SESSION['user']);
        $classes = get_registred_classes($dbconn,$user_id);
        $non_school_events = get_nonschool_events($dbconn,$user_id,'2023-05-01','2024-09-24');                                         
        $startDateTime = new DateTime($start_time);        
        $endDateTime = new DateTime($end_time);   
        $nonschool_event = ['time_start'=>$startDateTime->format('l')." ".explode(' ',$start_time)[1],'time_end'=>$endDateTime->format('l')." ".explode(' ',$end_time)[1]];
        if (check_free_days($date) && hasTimeConflict_Class_Class($nonschool_event, $classes)) {
            ?><div style="color:red; font-weight:bold"><?php echo 'Time conflict detected with classes. Your activity was not added.';?></div><?php
        } else if (check_time_conflict_non_school_event_vs_nonschool_event(['time_start'=>$start_time,'time_end'=>$end_time],$non_school_events)){
            ?><div style="color:red; font-weight:bold"><?php echo 'Time conflict detected with nonschool activity. The new activity was not added.';?></div><?php
        } else {
            insert_other_event($dbconn, $user_id, $event_name, $start_time, $end_time, $tag, $description);            
        }            
    }
    else {
        echo "<p> Please fill out all fields </p>";
    }
}
else {
    echo "<div class='logo'><img src='../images/ComeniusUniversity.png' alt='University'>";
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p> </div>";
    
}
?>
</section>

<?php
include('../templates/footer.php');
?>