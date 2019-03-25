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

            <?php include('../blocks/sidenav_u.php'); ?>


            <div id="content">

                <nav aria-label="breadcrumb">

                    <button type="button" id="sidebarCollapse" class="btn btn-info float-left mr-2" style="padding: 0.65rem 1rem;">
                   <i class="fas fa-align-left"></i>
               </button>

                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="type.php">Select Type</a></li>
                        <li class="breadcrumb-item disabled" aria-current="page">Create Request</li>
                        <li class="breadcrumb-item active" aria-current="page">Confirmation</li>
                    </ol>
                </nav>
                
                <div class="jumbotron">
                    <p>Your request has been posted with Request ID:</p>
                    <h1 class="display-2 text-success">
                       
                        <?php

                        if(isset($_SESSION['uid'])){
                            $uid = $_SESSION['uid'];

                            echo $uid;
                        }

                        ?> 
                        
                    </h1>
                    <p class="lead">To check the status of your request, please call the admin office.</p>
                    <hr class="my-4">
                    <p class="lead">
                        <a class="btn btn-primary btn-lg" href="type.php" role="button">OK</a>
                    </p>
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
                document.getElementById("").setAttribute("min", today);

            });

        </script>
    </body>

    </html>
