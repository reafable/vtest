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
    <link rel="stylesheet" href="../css/style2.css">
</head>

<body>
   <div class="wrapper">
       
       <?php include('../blocks/sidenav_u.php'); ?>
       
       <div id="content">
           
           <nav aria-label="breadcrumb">
               <ol class="breadcrumb">
                   <li class="breadcrumb-item active">Select Type</li>
                   <!-- <li class="breadcrumb-item disabled" aria-current="page">Create Request</li>
                   <li class="breadcrumb-item active" aria-current="page">Asset</li> -->
               </ol>
           </nav>
           
           <h2 class="mb-3">Choose Request Type</h2>
           
           <div class="row">
               <div class="col-md-6">
                   <a href="service.php" class="click-card">
                       <div class="card text-white bg-primary mb-3">
                           <!-- <div class="card-header">Pending Requests</div> -->
                           <div class="card-body">
                               <p class="card-text" style="font-size: 3em; color: #fff;">
                                   Service Request
                               </p>
                               <h5 class="card-title">Create</h5>
                           </div>
                       </div>
                   </a>

               </div>

               <div class="col-md-6">
                   <a href="asset.php" class="click-card">
                       <div class="card text-white bg-warning mb-3">
                           <!-- <div class="card-header">In-progress Requests</div> -->
                           <div class="card-body">
                               <p class="card-text" style="font-size: 3em; color: #fff;">
                                   Asset Request
                               </p>
                               <h5 class="card-title">Create</h5>
                           </div>
                       </div>
                   </a>

               </div>
           </div>
           
<!--            <div class="container h-100">
               <div class="row h-100 justify-content-center align-items-center">
                   <a href="service.php" class="btn btn-primary">Service</a>
                   &nbsp;
                   <a href="asset.php" class="btn btn-primary">Asset</a>
               </div>
           </div>
            -->           
       </div>
       
   </div>
    

    <script src="../js/jquery-3.3.1.slim.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>