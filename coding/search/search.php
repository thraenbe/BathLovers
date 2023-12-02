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
            <input id="course" name="course" class="input" placeholder="Search..." onclick="clearResults()">
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
                        <?php $someCondition = course_added($dbconn,$row['id']);?>
                        <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="Add" data-id="<?php echo $row['id']; ?>" <?php echo $someCondition ? 'disabled' : ''; ?>>
                        <!-- <input type="submit" name="class<?php echo $row['id']; ?>" class="add-btn" value="Add" data-id="<?php echo $row['id']; ?>"> -->
                    </div>
                    <div class="confirmation-message" id="confirmation<?php echo $row['id']; ?>"></div>
            <?php                    
                }
            }
            ?>
        </form>
    <?php            
        for ($i = 0; $i < 30; $i++) {
            $buttonName = 'class' . $i;                        
            if (isset($_POST[$buttonName])) {                 
                add_course($dbconn,$i,$username);
                echo "<div class='confirmation-message'>Class " . $i . " added successfully!</div>";                
            }
        }
    }
    ?>
</section>

<?php
include('../templates/footer.php');
?>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    function clearResults() {
        // Hide all confirmation messages
        var confirmationMessages = document.querySelectorAll('.confirmation-message');
        confirmationMessages.forEach(function(message) {
            message.style.display = 'none';
        });

        document.getElementById('course').value = '';
    }

    $(document).ready(function() {
        $('.add-btn').click(function(e) {
            e.preventDefault();

            var form = $('#searchForm');
            var url = form.attr('action');
            var courseId = $(this).data('id');
            var confirmationDiv = $('#confirmation' + courseId);

            $.ajax({
                type: 'POST',
                url: url,
                data: form.serialize(),
                success: function(response) {
                    // Handle the response if needed
                    confirmationDiv.html('Class ' + courseId + ' added successfully!');
                    confirmationDiv.show(); // Show the confirmation message
                }
            });
        });
    });
</script>
