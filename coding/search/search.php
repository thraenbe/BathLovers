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
.center {
    background-color: #DAF6F4;
    align-items: center; 
    height:41%; 
    width: 95%;} 
.title{
    font-size: 32px;
    font-weight: bolder;
    } 
.day1{
    text-align: middle;
    font-size: 24px;
    font-weight: bolder;
} 
.add-btn{
    align-items: center;
    font-weight: bold;
}
/* .input {
    width: 30%;    
} */
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
    font-size: 20px;
    color: green;
    font-weight: bold;
}
.course_link:hover{
    font-size: 16px;
    color: blue;
    font-weight: bold;
}
.conflict{            
    font-weight: bolder;
    font-size: 20;    
    border: none;      
    color: red;      
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
            // odporucane predmety
            foreach (get_recomended_subjects($dbconn,$user_id) as $recom_course){
                ?>
                <div class="recomend"><?php echo "Course: ".$recom_course['name_en']."  on ".$recom_course['time_start']."-".explode(" ",$recom_course['time_end'])[1]?></div>
                <?php                
            }
            // popis semestra
            echo "<div class='recomend' style='font-weight:bold'>Courses held in dates 18.9.2023-14.12.2023 and during 19.2.2024-16.5.2024</div>";
            // search            
            if (isset($_POST['Search'])) {
                $searchtext = $_POST['course'];
                $_SESSION['searchtext']=$searchtext;
                $rows = search_subjects($dbconn,$searchtext);                
                write_courses($dbconn,$user_id);
                
            }
            for ($i=0;$i<30;$i++){
                $buttonName = 'class' . $i;
                // nasiel som button                        
                if (isset($_POST[$buttonName])) {                                                            
                    add_course($dbconn, $i, $user_id);
                    $n_class_name = get_Subject($dbconn,$i)['name'];
                    echo "<p id='notification' class = 'good_notify'> <strong>✓ Class $n_class_name suscessfullly added to schedule </strong> </p>";                                                            
                    write_courses($dbconn,$user_id);
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
    }, 5000);
  });
</script>