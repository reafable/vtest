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
    <title>Requests | Service</title>

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
                   <li class="breadcrumb-item"><a href="type.php">Select Type</a></li>
                   <li class="breadcrumb-item disabled" aria-current="page">Create Request</li>
                   <li class="breadcrumb-item active" aria-current="page">Service</li>
               </ol>
           </nav>
           
           <div class="row">
               
                   <form class="col" action="service.php" method="post">
                       <div class="form-group">
                           <label for="custname">Customer Name</label>
                           <input type="text" class="form-control" id="custname" name="custname" placeholder="Enter customer name" required>
                       </div>
                       <div class="form-group">
                           <label for="compdate">Expected Date of Completion</label>
                           <input type="date" class="form-control" id="compdate" name="compdate" placeholder="Enter date" required>
                       </div>
                       <div class="form-group">
                           <label for="servicetext">Service Description</label>
                           <textarea class="form-control" id="servdesc" name="servdesc" rows="4" placeholder="Brief description of service" required></textarea>
                       </div>
                       <button type="submit" class="btn btn-primary" name="submitsr">Submit</button>
                   </form>
               
           </div>
           
       </div>
       
   </div>

    <script src="../js/jquery-3.3.1.slim.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>