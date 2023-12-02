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
        <a href="#">Day</a>
        <a href="#">Week</a>
        <a href="../calendar/add_to_schedule.php" >Add Activity</a>
        <a href="#"> Room plan </a>
        <strong>  Show classes </strong>
        <form method="post">
            <input type="checkbox" id="lectures" name="lectures" value="Lectures">
            <label for="lectures"> Lecturs</label><br>
            <input type="checkbox" id="labs" name="labs" value="Labs">
            <label for="labs"> Labs </label><br>
            <input type="checkbox" id="courses" name="courses" value="Courses">
            <label for="vehicle3"> Courses </label><br><br>
        </form>
        
</div>
    <script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>