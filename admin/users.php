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
        <title>Document</title>

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
                            <li class="breadcrumb-item active" aria-current="page">Users</li>
                        </ol>
                    </nav>


                    <div class="row">
                        <div class="col col-sm-12">


                            <h2>Users</h2>
                            <a class="ml-4 float-right" href="#addUser" data-toggle="modal"><button class="btn btn-primary">Add User</button></a>


                            <table id="usersTable" class="table table-striped">
                                <thead>
                                    <!-- <th>ID</th> -->
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Username</th>
                                    <th>User Type</th>
                                    <th>Status</th>
                                    <!-- <th>Password</th> -->
                                    <th>Actions</th>
                                </thead>
                                <tbody>
                                    <?php
                                    displayUsers();
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!--Modal-->
                    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-labelledby="addAsset>Label" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addAssetModalLabel">Add User</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                </div>
                                <div class="modal-body">
                                    <form action="users.php" id="addUserForm" method="post">
                                        <!-- <input type="hidden" name="addAssetReq" value="assetReq"> -->

                                        <div class="form-group">
                                            <label for="fname">First Name</label>
                                            <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter first name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="lname">Last Name</label>
                                            <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter last name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Username" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">User Type</label>
                                            <select class="form-control" name="user_type" required>
                                            <option value="" selected disabled>Select user type</option>
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Password</label>
                                            <input type="password" class="form-control" id="password1" name="password1" placeholder="Enter password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="name">Confirm Password</label>
                                            <input type="password" class="form-control" id="password2" name="password2" placeholder="Re-enter password" required>
                                        </div>
                                    </form>


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <input type="submit" form="addUserForm" class="btn btn-primary" value="Add" name="createUser">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
        
        <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>
        
        <script>
            $(document).ready(function() {
                $("#usersTable").DataTable({
                    pageLength: 25,
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
