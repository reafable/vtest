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
    <title>My Requests</title>

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
            include('../blocks/sidenav_u.php');
        ?>

        <div id="content">

            <nav aria-label="breadcrumb">
               
                <button type="button" id="sidebarCollapse" class="btn btn-info float-left mr-2" style="padding: 0.65rem 1rem;">
                    <i class="fas fa-align-left"></i>
                </button>
               
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="type.php">Home</a></li>
                    <li class="breadcrumb-item disabled" aria-current="page">Requests</li>
                    <li class="breadcrumb-item active" aria-current="page">My Requests</li>
                </ol>
            </nav>

            
                <div class="row">
                    <div class="col col-sm-12">
                        <h2>Service Requests</h2>
                        <table id="servicePooledTable" class="table table-striped">
                            <thead>
                                <th>Request ID</th>
                                <th>Customer Name</th>
                                <th>Posted On</th>
                                <th>Expected Completion</th>
                                <!-- <th>Service Type</th>
                                <th>Service Description</th> -->
                                <th>Status</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                <?php
                                    displayUserServiceRequests();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row mt-5">
                    <div class="col col-sm-12">
                        <h2>Asset Requests</h2>
                        <table id="assetPooledTable" class="table table-striped">
                            <thead>
                                <th>Request ID</th>
                                <th>Customer Name</th>
                                <th>Posted On</th>
                                <th>Expected Completion</th>
                                <!-- <th>Assets Required</th> -->
                                <th>Status</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                <!--<tr>-->
                                <?php
                                    
                                    displayUserAssetRequests();
                                    
                                    ?>

                                <!-- reject modal -->
                                <div class="modal fade" id="reject<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="reject<?php echo $id; ?>Label" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <form method="post">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectModalLabel">Reject Request</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="reject_req_id" value="<?php echo $id; ?>">
                                                    <p>Are you sure you want to reject this request?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger" name="reject">Reject</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- edit modal -->
                                <div class="modal fade" id="edit<?php echo $id; ?>" tabindex="-1" role="dialog" aria-labelledby="reject<?php echo $id; ?>Label" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectModalLabel">Edit Request</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="edit_req_id" value="<?php echo $id; ?>">
                                                <p>Are you sure you want to edit this request?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary" name="save">Save changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php tRowClose(); ?>

                                <!--</tr>-->

                            </tbody>
                        </table>
                    </div>
                </div>
            
        </div>
    </div>
    
    <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#servicePooledTable").DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50, 100]
            });
            $("#assetPooledTable").DataTable({
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
