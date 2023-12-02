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
    ?>
        <form id="searchForm" method="post">
            <input id="course" name="course" class="input" placeholder="Search...">
            <input id="Search" type="submit" name="Search" value="Search">
            <?php
            if (isset($_POST['Search'])) {
                $searchtext = $_POST['course'];
                $_SESSION['searchtext']=$searchtext;
                $rows = search_subjects($dbconn,$searchtext);                
                foreach ($rows as $row) {
            ?>                    
                    <div class="card">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <div class="start"><?php echo $row['time_start'] ?></div>
                        <div class="end"><?php echo $row['time_end'] ?></div>
                        <div class="end">Teacher: <?php echo $row['teacher'] ?></div>
                        <?php $someCondition = course_added($dbconn,$row['id']);?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="Add" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                    </div>
            <?php                                        
                }
            }
            for ($i=0;$i<30;$i++){
                $buttonName = 'class' . $i;                        
                if (isset($_POST[$buttonName])) {
                    add_course($dbconn, $i, $username);
                    $rows = search_subjects($dbconn,$_SESSION['searchtext']);
                    foreach ($rows as $row) {
                    ?>
                    <div class="card">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <div class="start"><?php echo $row['time_start'] ?></div>
                        <div class="end"><?php echo $row['time_end'] ?></div>
                        <div class="end">Teacher: <?php echo $row['teacher'] ?></div>
                        <?php $someCondition = course_added($dbconn,$row['id']);?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="Add" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                    </div>
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
