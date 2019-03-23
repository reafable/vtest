<?php
include('../functions.php');
if (!isLoggedIn()) {
	$_SESSION['msg'] = "You must log in first";
	header('location: ../login.php');
}
if (isAdmin()) {
	header('location: ../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/formcenter.css">
</head>

<body>
    <div class="container h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <a href="service.php" class="btn btn-primary">Service</a>
            &nbsp;
            <a href="asset.php" class="btn btn-primary">Asset</a>
        </div>
    </div>

    <script src="../js/jquery-3.3.1.slim.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>