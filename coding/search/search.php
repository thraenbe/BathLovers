<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Login');
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
                $rows = search_subjects($dbconn,$searchtext);                
                foreach ($rows as $row) {
            ?>                    
                    <div class="card">
                        <div class="title"><?php echo $row['name_en'] ?></div>
                        <div class="start"><?php echo $row['time_start'] ?></div>
                        <div class="end"><?php echo $row['time_end'] ?></div>
                        <div class="end">Teacher: <?php echo $row['teacher'] ?></div>
                        <?php $someCondition = course_added($dbconn,$row['id']);                        
                        $buttonName = 'class' . $row['id'];                        
                        echo $buttonName;
                        if (isset($_POST[$buttonName])) {
                            echo "sjsdok";
                            add_course($dbconn, $row['id'], $username);
                        }
                        ?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="Add" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                    </div>
            <?php                                        
                }
            }
            ?>
        </form>
    <?php                     
    }
    // ?>
</section>
<?php
include('../templates/footer.php');
?>
