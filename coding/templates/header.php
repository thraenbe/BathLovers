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
</head>
<body>
    <header>
		<h1><?php echo $title; ?></h1>
	</header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="btn btn-light navbar-brand" href="#">
                <img src="../icons\icons\navbar\languege.png" alt="Logo" class="d-inline-block align-text-top">
            </a>          
            <a class="btn btn-light navbar-brand" href="../calendar/add_to_schedule.php">
                <img src="../icons\icons\tabbar\new.png" alt="Logo" class="d-inline-block align-text-top">
            </a>          
        </div>
    </nav>
<?php         
}
?>

