<?php 
function get_header($title) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
    h1 {
        text-align: center;
        font-weight: bold;
    }
    .dropdown {
    position: relative;
    display: inline-block;
    }

    .dropdown-content {
    display: none;
    position: absolute;
    background-color: #f1f1f1;
    min-width: 160px;
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
    }

    .dropdown-content a {
    color: black;
    padding: 10px 10px;
    text-decoration: none;
    display: block;
    }

    .dropdown-content a:hover {background-color: #ddd;}

    .dropdown:hover .dropdown-content {display: block;}

</style>
</head>
<body>
    <header>
		<h1><?php echo $title; ?></h1>
	</header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid d-flex justify-content-between">
        <a class="btn btn-light navbar-brand" onclick="openNav()">
            <img src="../icons/icons/navbar/languege.png" alt="Logo" class="d-inline-block align-text-top">
        </a>          
        <div>
        <div class="dropdown">
                <button class="btn btn-light navbar-brand" >
                    <img src="../icons/icons/tabbar/new.png" alt="Logo" class="d-inline-block align-text-top">
                    <div class="dropdown-content">
                        <a href="../search/search.php">Search classes</a>
                        <a href="../calendar/add_to_schedule.php">Add activity</a>
                    </div>
            </div>
            <a class="btn btn-light navbar-brand" href="../search/search.php">
                <img src="../icons/icons/tabbar/search.png" alt="Logo" class="d-inline-block align-text-top">
            </a>
        </div>
    </div>
</nav>

<?php 
include('sidenav.php');         
}
?>

