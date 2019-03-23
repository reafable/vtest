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


    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/formcenter.css">

    <script src="../js/jquery-3.3.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <link href="../css/select2.min.css" rel="stylesheet">
    <script src="../js/select2.full.min.js"></script>


</head>

<body>
    <div class="container h-100">
        <div class="row h-100 justofy-content-center align-items-center">
            <form class="col-md-8 mx-auto" action="asset.php" method="post">
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
                            <select class="form-control srselect" name="assetdesc[]">

                                <?php
                        
                        populateAssetSelect();
                        
                        ?>

                            </select>
                        </div>
                        <div class="col">

                            <label for="qty">Quantity</label>
                            <input type="text" class="form-control" placeholder="Enter asset quantity">
                        </div>



                    </div>




                </div>
                <button id="addMoreAssets" class="btn btn-warning">Add More Assets</button>
                <button type="submit" class="btn btn-primary" name="submitar">Submit</button>
            </form>
        </div>
    </div>

    <!-- <script src="../js/jquery-3.3.1.slim.min.js"></script> -->


    <script>
        $(document).ready(function() {
            $('.srselect').select2();
            $('#addMoreAssets').click(function(e) {
                var pop = "<?php populateAssetSelect(); ?>";
                e.preventDefault();
                $("#assetSelect").append("<div class='form-row'> <div class='col'> <label for='assetdesc'>Asset</label> <select class='form-control srselect' name='assetdesc[]'>" + pop + "</select> </div> <div class='col'> <label for='qty'>Quantity</label> <input type='text' class='form-control' placeholder='Enter asset quantity'> </div> </div>");
                $('.srselect').select2();
            })
        })

    </script>
</body>

</html>
