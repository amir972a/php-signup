<!DOCTYPE html>

<?php
session_start();
if(isset($_SESSION['name'])){
    $name=$_SESSION['name'];
}else{
    header("location:index.php");
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <title>Welcome</title>
</head>
<body>
    <?php
    echo "<h3 class='text-center'>سلام $name</h3>";
    ?>
</body>
</html>