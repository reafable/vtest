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
        <title>Requests Overview</title>

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
                            <li class="breadcrumb-item active" aria-current="page">Overview</li>
                        </ol>
                    </nav>


                    <div class="row">
                        <div class="col col-sm-12">


                            <h2>Requests Overview</h2>
                            <!-- <a class="ml-4" href="#addAsset" data-toggle="modal"><button class="btn btn-primary">Add Asset</button></a> -->



                            <table id="assetsTable" class="table table-striped">
                                <thead>
                                    <th>Request ID</th>
                                    <th>Type</th>
                                    <th>Customer Name</th>
                                    <th>Posted On</th>
                                    <th>Posted By</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                                    <?php
                                    displayRequests();
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--Modal-->
                    <div class="modal fade" id="addAsset" tabindex="-1" role="dialog" aria-labelledby="addAsset>Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addAssetModalLabel">Add Asset</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                    <form action="assets.php" id="addAssetForm" method="post">
                                        <!-- <input type="hidden" name="addAssetReq" value="assetReq"> -->

                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter asset name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Brief description of asset" required></textarea>
                                        </div>
                                    </form>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <input type="submit" form="addAssetForm" class="btn btn-primary" value="Add" name="createAsset">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
        
        <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>
        
        <script>
            $(document).ready(function() {
                $("#assetsTable").DataTable({
                    //pageLength: 25,
                    //scrollY: 550,
                    //scrollCollapse: true
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
