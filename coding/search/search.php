<head>
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
            <?php
            if (isset($_POST['Search'])) {
                $searchtext = $_POST['course'];
                $_SESSION['searchtext']=$searchtext;
                $rows = search_subjects($dbconn,$searchtext);                
                foreach ($rows as $row) {
            ?>                    
                    <div class="center">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <div class="start"><?php echo $row['time_start'] ?></div>
                        <div class="end"><?php echo $row['time_end'] ?></div>
                        <div class="end">Teacher: <?php echo $row['teacher'] ?></div>
                        <?php                         
                        $someCondition = course_added($dbconn,$row['id'],$user_id);
                        ?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="Add" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                    </div> <br>
            <?php                                        
                }
            }
            for ($i=0;$i<30;$i++){
                $buttonName = 'class' . $i;                        
                if (isset($_POST[$buttonName])) {
                    add_course($dbconn, $i, $user_id);
                    $rows = search_subjects($dbconn,$_SESSION['searchtext']);
                    foreach ($rows as $row) {
                    ?>
                    <div class="center">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <div class="start"><?php echo $row['time_start'] ?></div>
                        <div class="end"><?php echo $row['time_end'] ?></div>
                        <div class="end">Teacher: <?php echo $row['teacher'] ?></div>
                        <?php $someCondition = course_added($dbconn,$row['id'],$user_id);?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn"  value="Add" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
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
