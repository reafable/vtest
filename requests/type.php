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
    <title>Hello, <?php displayCurrentUser(); ?></title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/formcenter.css">
    <link rel="stylesheet" href="../css/style2.css">
    
    <link rel="stylesheet" href="../css/jquery.mCustomScrollbar.min.css">
    
    <script src="../js/fa-solid.js"></script>
    <script defer src="../js/fontawesome.js"></script>
    
</head>

<body>
   <div class="wrapper">
       
       <?php include('../blocks/sidenav_u.php'); ?>
       
       <div id="content">
           
           <nav aria-label="breadcrumb">
              
               <button type="button" id="sidebarCollapse" class="btn btn-info float-left mr-2" style="padding: 0.65rem 1rem;">
                   <i class="fas fa-align-left"></i>
               </button>
               
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
    <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>
    
    <script type="text/javascript">
    
        $(document).ready(function () {

            $("#sidebar").mCustomScrollbar({
                theme: "minimal"
            });

            $('#sidebarCollapse').on('click', function () {
                $('#sidebar, #content').toggleClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
            
            var today = new Date();
            var yyyy = today.getFullYear();
            var mm = today.getMonth()+1;
            var dd = today.getDate();
            if(mm < 10){
                mm = '0' + mm
            }
            if(dd < 10){
                dd = '0'+ dd
            }
            today = yyyy+'-'+mm+'-'+dd;
            document.getElementById("").setAttribute("min", today);

        });
        
    </script>
    
</body>

</html>