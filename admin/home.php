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
    <title>Hello, <?php displayCurrentUser(); ?></title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style2.css">
</head>

<body>
    <div class="wrapper">
        <?php
    
    include('../blocks/sidenav.php');
    
    ?>

        <div id="content">

            <nav aria-label="breadcrumb">
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
                                    <?php displayCountPooled(); ?>
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
                                    <?php displayCountPending(); ?>
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
                                    <?php displayCountInProgress(); ?>
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
                                    <?php displayCountCompleted(); ?>
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
</body>

</html>
