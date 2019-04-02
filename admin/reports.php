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

        <script src="../js/Chart.min.js"></script>

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



                <!-- <div class="container"> -->
                
                <div class="row mt-5">
                
                <div class="col-6">
                
                <div class="card">
                
                <div class="card-body">
                
                <canvas id="requestTypes"></canvas>

                <h5 class="card-title">Request Types</h5>
                
                </div>
                
                </div>
                
                </div>
                <div class="col-6">
                
                <div class="card">
                
                <div class="card-body">
                
                <canvas id="serviceTypes"></canvas>
                
                <h5 class="card-title">Service Types</h5>
                
                </div>
                
                </div>
                
                </div>
                
                </div>
                <div class="row mt-5">
                
                <div class="col-6">
                
                <div class="card">
                
                <div class="card-body">
                
                <canvas id="requestBranches"></canvas>

                <h5 class="card-title">Branch Requests</h5>
                
                </div>
                
                </div>
                
                </div>
                <div class="col-6">
                
                <div class="card">
                
                <div class="card-body">
                
                <canvas id="completionRate"></canvas>

                <h5 class="card-title">Request Types</h5>
                
                </div>
                
                </div>
                
                </div>
                
                </div>
                
                <!-- </div> -->



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

            
            

            let assetCount = <?php displayCountAssetFromTo(); ?>;
            let serviceCount = <?php displayCountServiceFromTo(); ?>;
            let itCount = <?php displayCountITFromTo(); ?>;
            let d2dCount = <?php displayCountD2DFromTo(); ?>;
            let carCount = <?php displayCountCarFromTo(); ?>;
            let ncrCount = <?php displayCountNCRFromTo(); ?>;
            let nlCount = <?php displayCountNLFromTo(); ?>;
            let slCount = <?php displayCountSLFromTo(); ?>;
            let vCount = <?php displayCountVFromTo(); ?>;
            let mCount = <?php displayCountMFromTo(); ?>;
            let poolCount = <?php displayCountPoolFromTo(); ?>;
            let pendingCount = <?php displayCountPendingFromTo(); ?>;
            let ongoingCount = <?php displayCountInProgressFromTo(); ?>;
            let completedCount = <?php displayCountCompletedFromTo(); ?>;

            let requestTypes = document.getElementById('requestTypes').getContext('2d');

            let requestTypesChart = new Chart(requestTypes, {
                type:'doughnut',
                data:{
                    labels:['Asset', 'Service'],
                    datasets:[{
                        label:'Requests Type',
                        data:[assetCount, serviceCount],
                        backgroundColor:['#f4ce22','#164c9e']
                    }]
                }
            });

            let serviceTypes = document.getElementById('serviceTypes').getContext('2d');

            let serviceTypesChart = new Chart(serviceTypes, {
                type:'doughnut',
                data:{
                    labels:['IT', 'Day-to-day', 'Car'],
                    datasets:[{
                        label:'Service Types',
                        data:[itCount, d2dCount, carCount],
                        backgroundColor:['#f4ce22','#164c9e','#1ca05c']
                    }]
                }
            });

            let requestBranches = document.getElementById('requestBranches').getContext('2d');

            let requestBranchesChart = new Chart(requestBranches, {
                type:'doughnut',
                data:{
                    labels:['NCR', 'North Luzon', 'South Luzon', 'Visayas', 'Mindanao'],
                    datasets:[{
                        label:'Requesting Branches',
                        data:[ncrCount, nlCount, slCount, vCount, mCount],
                        backgroundColor:['#FF530D','#E82C0C','FF0000','E80C7A','FF0DFF']
                    }]
                }
            });

            let completionRate = document.getElementById('completionRate').getContext('2d');

            let completionRateChart = new Chart(completionRate, {
                type:'doughnut',
                data:{
                    labels:['Completed', 'Ongoing', 'Pending', 'Pooled'],
                    datasets:[{
                        label:'Completion Rate',
                        data:[completedCount, ongoingCount, pendingCount, poolCount]
                    }]
                }
            });

        </script>
    </body>

</html>
