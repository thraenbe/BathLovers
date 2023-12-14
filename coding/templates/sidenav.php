<style>
    .sidenav {
    width: 0;
    position: fixed;
    z-index: 1;
    top: 0;
    left: 0;
    background-color: #ffffff;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 10px;
    height: 100%;
}

.sidenav a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 16px;
    color: #333300;
    display: block;
    transition: 0.3s;
}

.sidenav a:hover {
    color: #6600ff;
}

.sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
}
</style>
<div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <h4> My Schedule </h4>
        <a href="../calendar/daily.php">Day</a>
        <a href="../calendar/weekly.php">Week</a>
        <a href="../calendar/add_to_schedule.php" >Add Activity</a>
        <a href="../images/roomplan.jpg"> Room plan </a>
        <a href="../search/search.php"> Search classes </a>
       
        <?php
        if(isset($_SESSION["user"])) {
            if ($title === "Day" ||  $title === "Week") {
                ?>
                <strong>  Show events </strong>
                <form method="post">
                    <input type="checkbox" id="lectures" name="lectures" value="Lectures">
                    <label for="school"> Classes</label><br>
                    <input type="checkbox" id="other" name="other" value="Other">
                    <label for="other"> Other events </label><br>                    
                </form>                
                <form action="../login/login.php" method="post">
                <input name="logout" type="submit" id="odhlas" value="Logout"> 
                </form>
                
            <?php
            }
            if (isset($_POST['logout'])){
                session_unset();	            
            }
            ?>                
        
        <!-- <form action="../login/login.php" method="post">
            <input name="logout" type="submit" id="odhlas" value="Logout" class="logoutbtn">                
        </form> -->
        <?php
         
        }
        
        ?>
        
</div>
<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>