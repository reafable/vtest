<?php
include('../functions.php');
if (!isLoggedIn()) {
	$_SESSION['msg'] = "You must log in first";
	header('location: ../index.php');
}
if (!isAdmin()) {
	header('location: ../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Completed Requests</title>

    <script src="../js/jquery-3.3.1.min.js"></script>

    <script type="text/javascript" src="../js/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/datatables.min.css">
    <script src="../js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/style2.css">
    <link rel="stylesheet" href="../css/all.css">
    
    <link rel="stylesheet" href="../css/jquery.mCustomScrollbar.min.css">

    <script src="../js/fa-solid.js"></script>
    <script defer src="../js/fontawesome.js"></script>
</head>

<body>
    <div class="wrapper">
        <?php
            include('../blocks/sidenav.php');
        ?>

        <div id="content">

            <nav aria-label="breadcrumb">
               
                <button type="button" id="sidebarCollapse" class="btn btn-info float-left mr-2" style="padding: 0.65rem 1rem;">
                    <i class="fas fa-align-left"></i>
                </button>
               
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item disabled" aria-current="page">Requests</li>
                    <li class="breadcrumb-item active" aria-current="page">Completed</li>
                </ol>
            </nav>

           
                <div class="row">
                    <div class="col col-sm-12">
                        <h2>Service Requests</h2>
                        <table id="serviceCompletedTable" class="table table-striped">
                            <thead style="word-break:break-word; font-size:60%;">
                                <?php
                                
                                if($_SESSION['user']['user_type'] == 'sadmin'){

                                    echo
                                    "<th>Customer Name</th>
                                    <th>Posted On</th>
                                    <th>Posted By</th>
                                    <th>Expected Completion</th>
                                    <th>Target Completion</th>
                                    <th>Pending On</th>
                                    <th>Pending By</th>
                                    <th>Ongoing On</th>
                                    <th>Ongoing By</th>
                                    <th>Actual Completion</th>
                                    <th>Completed By</th>
                                    <th>Service Type</th>
                                    <th>Service Description</th>
                                    <th>Actions</th>";

                                }else{

                                    echo
                                    "<th>Customer Name</th>
                                    <th>Posted On</th>
                                    <th>Posted By</th>
                                    <th>Expected Completion</th>
                                    <th>Target Completion</th>
                                    <th>Pending On</th>
                                    <th>Ongoing On</th>
                                    <th>Actual Completion</th>
                                    <th>Service Type</th>
                                    <th>Service Description</th>
                                    <th>Actions</th>";

                                }
                                
                                ?>
                                <!-- <th>Customer Name</th>
                                <th>Posted On</th>
                                <th>Posted By</th>
                                <th>Expected Completion</th>
                                <th>Target Completion</th>
                                <th>Pending On</th>
                                <th>Pending By</th>
                                <th>Ongoing On</th>
                                <th>Ongoing By</th>
                                <th>Actual Completion</th>
                                <th>Completed By</th>
                                <th>Service Type</th>
                                <th>Service Description</th>
                                <th>Actions</th> -->
                            </thead>
                            <tbody>
                                <?php
                                    displayServiceCompleted();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col col-sm-12">
                        <h2>Asset Requests</h2>
                        <table id="assetCompletedTable" class="table table-striped">
                            <thead style="word-break:break-word; font-size:60%;">
                                <?php
                                
                                if($_SESSION['user']['user_type'] == 'sadmin'){
                                    
                                    echo
                                    "<th>Customer Name</th>
                                    <th>Posted On</th>
                                    <th>Posted By</th>
                                    <th>Expected Completion</th>
                                    <th>Target Completion</th>
                                    <th>Pending On</th>
                                    <th>Pending By</th>
                                    <th>Ongoing On</th>
                                    <th>Ongoing By</th>
                                    <th>Actual Completion</th>
                                    <th>Completed By</th>
                                    <th>Assets Required</th>
                                    <th>Actions</th>";

                                }else{

                                    echo
                                    "<th>Customer Name</th>
                                    <th>Posted On</th>
                                    <th>Posted By</th>
                                    <th>Expected Completion</th>
                                    <th>Target Completion</th>
                                    <th>Pending On</th>
                                    <th>Ongoing On</th>
                                    <th>Actual Completion</th>
                                    <th>Assets Required</th>
                                    <th>Actions</th>";

                                }

                                ?>
                                <!-- <th>Customer Name</th>
                                <th>Posted On</th>
                                <th>Posted By</th>
                                <th>Expected Completion</th>
                                <th>Target Completion</th>
                                <th>Actual Completion</th>
                                <th>Assets Required</th>
                                <th>Actions</th> -->
                            </thead>
                            <tbody>
                                <?php
                                   displayAssetCompleted(); 
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
          

        </div>
    </div>
    
    <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $("#serviceCompletedTable").DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50, 100]
            });
            $("#assetCompletedTable").DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50, 100]
            });
            
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