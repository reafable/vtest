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
        <title>Requests | Asset</title>

        <script src="../js/jquery-3.3.1.min.js"></script>
        <script src="../js/bootstrap.bundle.min.js"></script>
        
        
        <link rel="stylesheet" href="../css/bootstrap.css">
        <link rel="stylesheet" href="../css/formcenter.css">
        <link rel="stylesheet" href="../css/style2.css">
        

        <script src="../js/fa-solid.js"></script>
        <script defer src="../js/fontawesome.js"></script>

        <link href="../css/select2.min.css" rel="stylesheet">
        <script src="../js/select2.full.min.js"></script>

        <link rel="stylesheet" href="../css/jquery.mCustomScrollbar.min.css">

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
                               <li class="breadcrumb-item active" aria-current="page">Asset</li>
                           </ol>
                       </nav>
                   
                   <div class="row">
                       <form class="col" action="asset.php" method="post">
                           <div class="form-group">
                               <label for="custname">Customer Name</label>
                               <input type="text" class="form-control" id="custname" name="custname" placeholder="Enter customer name" required>
                           </div>
                           <div class="form-group">
                               <label for="compdate">Expected Date of Completion</label>
                               <input type="date" class="form-control" id="compdate" name="compdate" placeholder="Enter date" required>
                           </div>
                           <div id="assetSelect" class="form-group">

                               <div class="form-row">

                                   <div class="col">

                                       <label for="assetdesc">Asset</label>
                                       <select class="form-control srselect" name="assetdesc[]" required>

                                           <?php

                                           populateAssetSelect();

                                           ?>

                                       </select>
                                   </div>
                                   <div class="col">

                                       <label for="qty">Quantity</label>
                                       <input type="text" class="form-control" name="assetQuantity[]" placeholder="Enter asset quantity" required>
                                   </div>



                               </div>




                           </div>
                           <div class="form-group">
                               <button id="addMoreAssets" class="btn btn-warning">Add More Assets</button>
                           </div>
                           <button type="submit" class="btn btn-primary" name="submitar">Submit</button>
                       </form>
                   </div>
           </div>
       </div>
       
       

        <!-- <script src="../js/jquery-3.3.1.slim.min.js"></script> -->
        
        <script src="../js/jquery.mCustomScrollbar.concat.min.js"></script>
        
        <script type="text/javascript">
            
            $(document).ready(function() {
                $('.srselect').select2();
                $('#addMoreAssets').click(function(e) {
                    var pop = "<?php populateAssetSelect(); ?>";
                    e.preventDefault();
                    $("#assetSelect").append("<div class='form-row'> <div class='col'> <label for='assetdesc'>Asset</label> <select class='form-control srselect' name='assetdesc[]' required>" + pop + "</select> </div> <div class='col'> <label for='qty'>Quantity</label> <input type='text' class='form-control' name='assetQuantity[]' placeholder='Enter asset quantity' required> </div> </div>");
                    $('.srselect').select2();
                });
                
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
                document.getElementById("compdate").setAttribute("min", today);
            });
        </script>
    </body>
    </html>