<?php
include('../functions.php');
if (!isLoggedIn()) {
    $_SESSION['msg'] = "You must log in first";
    header('location: ../login.php');
}
if (!isAdmin()) {
    header('location: ../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Request Posted</title>

        <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../css/formcenter.css">
        <link rel="stylesheet" href="../css/style2.css">

        <link rel="stylesheet" href="../css/jquery.mCustomScrollbar.min.css">

        <script src="../js/fa-solid.js"></script>
        <script defer src="../js/fontawesome.js"></script>
    </head>

    <body>

        <div class="wrapper">

            <?php include('../blocks/sidenav.php'); ?>


            <div id="content">

                <nav aria-label="breadcrumb">

                    <button type="button" id="sidebarCollapse" class="btn btn-info float-left mr-2" style="padding: 0.65rem 1rem;">
                        <i class="fas fa-align-left"></i>
                    </button>

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                        <li class="breadcrumb-item disabled" aria-current="page">Requests</li>
                        <li class="breadcrumb-item active" aria-current="page">Reports</li>
                    </ol>
                </nav>
                
                <form action="reports.php" method="post">
                    
                    <div class="form-row">
                        
                        <div class="form-group col" >
                            
                            <label for="dateRange1">From</label>
                            <input type="date" class="form-control" id="dateRange1" name="dateRange1" required>
                            
                        </div>
                        
                        <div class="form-group col" >
                            
                            <label for="dateRange2">To</label>
                            <input type="date" class="form-control" id="dateRange2" name="dateRange2" required>
                            
                        </div>
                        
                        <div class="form-group col-0 align-self-end">
                           
                            <button type="submit" class="btn btn-primary" id="dateRangeButton" name="dateRangeButton">Go</button>
                               
                        </div>
                        
                        
                    </div>
                    
                </form>

                <div class="jumbotron">
                    <h2>Summary of Requests</h2>
                    <p class="text-dark">From: <?php if(isset($_POST['dateRange1'])){ $d1=$_POST['dateRange1']; echo $d1;} ?></p>
                    <p class="text-dark">To: <?php if(isset($_POST['dateRange2'])){ $d2=$_POST['dateRange2']; echo $d2;} ?></p>
                    <hr class="my-4">
                    <p class="text-dark">Total requests: <?php displayCountAllFromTo(); ?></p>
                    <p class="text-dark">Pooled requests: <?php displayCountPoolFromTo(); ?></p>
                    <p class="text-dark">Pending requests: <?php displayCountPendingFromTo(); ?></p>
                    <p class="text-dark">Ongoing requests: <?php displayCountInProgressFromTo(); ?></p>
                    <p class="text-dark">Completed requests: <?php displayCountCompletedFromTo(); ?></p>
                    <p class="text-dark">Rejected requests: <?php displayCountRejectedFromTo(); ?></p>
                    
                    <h1 class="display-2 text-success">

                        

                    </h1>
                    <!-- <p class="lead">Hello. Goodbye. Genevieve.</p> -->
                    <!-- <hr class="my-4">
                    <p class="lead">
                        <a class="btn btn-primary btn-lg" href="" role="button">OK</a>
                    </p> -->
                </div>



            </div>

        </div>

        <script src="../js/jquery-3.3.1.slim.min.js"></script>
        <script src="../js/bootstrap.bundle.min.js"></script>
        <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>

        <script type="text/javascript">
            $(document).ready(function() {

                $("#sidebar").mCustomScrollbar({
                    theme: "minimal"
                });

                $('#sidebarCollapse').on('click', function() {
                    $('#sidebar, #content').toggleClass('active');
                    $('.collapse.in').toggleClass('in');
                    $('a[aria-expanded=true]').attr('aria-expanded', 'false');
                });

                var today = new Date();
                var yyyy = today.getFullYear();
                var mm = today.getMonth() + 1;
                var dd = today.getDate();
                if (mm < 10) {
                    mm = '0' + mm
                }
                if (dd < 10) {
                    dd = '0' + dd
                }
                today = yyyy + '-' + mm + '-' + dd;
                
                if($("#dateRange1").length){
                    
                    document.getElementById("dateRange1").setAttribute("max", today);
                    
                }
                
                if($("#dateRange2").length){
                    
                    document.getElementById("dateRange2").setAttribute("max", today);
                    
                }
                
            });

        </script>
    </body>

</html>
