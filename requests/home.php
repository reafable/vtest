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
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style2.css">
    
    <link rel="stylesheet" href="../css/jquery.mCustomScrollbar.min.css">

    <script src="../js/fa-solid.js"></script>
    <script defer src="../js/fontawesome.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php
    
    include('../blocks/sidenav_u.php');
    
    ?>

        <div id="content">

            <nav aria-label="breadcrumb">
               
                <button type="button" id="sidebarCollapse" class="btn btn-info float-left mr-2" style="padding: 0.65rem 1rem;">
                    <i class="fas fa-align-left"></i>
                </button>
               
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Home</li>
                </ol>
            </nav>

            <div class="greetum">
                <p class="greetline" style="font-size: 5em">Hello,
                    <?php displayCurrentUser(); ?>
                </p>
                <p style="font-size: 3em">you have:</p>
            </div>



            <div class="row">
                <div class="col-md-6">
                    <a href="pool.php" class="click-card">
                        <div class="card text-white bg-danger mb-3">
                            <!-- <div class="card-header">Pending Requests</div> -->
                            <div class="card-body">
                                <p class="card-text" style="font-size: 3em; color: #fff;">
                                    <?php displayCountUserPooled(); ?>
                                </p>
                                <h5 class="card-title">Requests in Pool</h5>
                            </div>
                        </div>
                    </a>

                </div>

                <div class="col-md-6">
                    <a href="pending.php" class="click-card">
                        <div class="card text-white bg-warning mb-3">
                            <!-- <div class="card-header">In-progress Requests</div> -->
                            <div class="card-body">
                                <p class="card-text" style="font-size: 3em; color: #fff;">
                                    <?php displayCountUserPending(); ?>
                                </p>
                                <h5 class="card-title">Pending Requests</h5>
                            </div>
                        </div>
                    </a>

                </div>


            </div>

            <div class="row">
                <div class="col-md-6">
                    <a href="ongoing.php" class="click-card">
                        <div class="card text-white bg-primary mb-3">
                            <!-- <div class="card-header">Pending Requests</div> -->
                            <div class="card-body">
                                <p class="card-text" style="font-size: 3em; color: #fff;">
                                    <?php displayCountUserInProgress(); ?>
                                </p>
                                <h5 class="card-title">Ongoing Requests</h5>
                            </div>
                        </div>
                    </a>

                </div>

                <div class="col-md-6">
                    <a href="completed.php" class="click-card">
                        <div class="card text-white bg-success mb-3">
                            <!-- <div class="card-header">In-progress Requests</div> -->
                            <div class="card-body">
                                <p class="card-text" style="font-size: 3em; color: #fff;">
                                    <?php displayCountUserCompleted(); ?>
                                </p>
                                <h5 class="card-title">Completed Requests</h5>
                            </div>
                        </div>
                    </a>

                </div>


            </div>



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

        });

    </script>
</body>

</html>
