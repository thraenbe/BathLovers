<?php
session_start();
include('../templates/header.php');
include('../util/db.php');
include('../util/functions.php');
get_header('Room plan');
?>
<style>
figure {
border: 1px #cccccc solid;
padding: 4px;
margin: auto;
}

img {
height: auto; 
width: auto;
}


figcaption {
background-color: black;
color: white;
padding: 2px;
text-align: center;
}
</style>
<section>
    <figure>
        <figcaption> <h2>The room plan of the faculty buildings </h2></figcaption>
        <img src="../images/roomplan.jpg" alt="Plan">
    </figure>
</section>
<?php
include('../templates/footer.php');
?>