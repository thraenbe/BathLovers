<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Add to schedule');
?>
<section>
<?php
if (isset($_SESSION['user'])) {
?>
<form method="post">
    <label for="eventName"> Event name</label>
    <input name="eventName"type="text" size="30" maxlength="30" id=eventName
    value="<?php if(isset($_POST['eventName'])) echo $_POST['eventName']; ?>"><br>
    <label for="date">Date</label>
    <input type="date" id="date" name="date"> <br>
    <label for="start">Start time:</label>
    <input type="time" id="start" name="start"> <br>
    <label for="end">End time:</label>
    <input type="time" id="end" name="end"> <br>
    <label for="tag"> Tag </label>
    <select name="tag" id="tag">
        <option value="work" selected>Work </option>
        <option value="sport" >Sport </option>
        <option value="dining" >Dining </option>
        <option value="other">Other </option>
    </select> <br>
   

    <label for="description"> Description</label>
    <textarea id="description" placeholder="Enter description..." name="description" rows="2" cols="20">
    </textarea> <br>
    <input name="add" type="submit" value="Add to schedule">



</form>
<?php
    if(isset($_POST['add'])) {
        if(isset($_POST['eventName']) 
        && isset( $_POST['date']) 
        && isset( $_POST['start'])
        && isset( $_POST['end']) 
        && isset( $_POST['tag']) 
       
        && isset( $_POST['description'])){
            $user_id = get_user_id($dbconn, $_SESSION['user']);
            $time_start = $_POST['date'] .' '.  $_POST['start'];
            $time_end = $_POST['date'] . ' ' . $_POST['end'];
            insert_other_event(
                $dbconn, $user_id, $_POST['eventName'], 
                $time_start, $time_end, 
                $_POST['tag'], $_POST['description'] );


        }
        else {
            echo "<p> Please fill out all fields </p> ";
        }
    }
}
else {
    echo "<p> You are not logged in. Please go to <a href='../login/login.php'> Login page </a> </p>";
    
}
?>
</section>

<?php
include('../templates/footer.php');
?>