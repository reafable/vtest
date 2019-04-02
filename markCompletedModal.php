<div class='modal fade' id='edit<?php echo $id?>' tabindex='-1' role='dialog' aria-labelledby='edit<?php echo $id?>Label' aria-hidden='true'>
    <div class='modal-dialog' role='document'>
        <form method='post'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='editModalLabel'>Request Details</h5>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                        <span aria-hidden='true'>&times;</span>
                    </button>
                </div>
                <div class='modal-body'>
                    <input type='hidden' name='edit_req_id' value='<?php echo $id ?>'>
                    <p>Customer Name: <?php echo $custname ?></p>
                    <p>Posted On: <?php echo $postdate ?></p>
                    <p>Posted By: <?php echo $postby ?></p>
                    <p>Expected Completion: <?php echo $compdate ?></p>
                    <p>Target Completion: <?php echo $actdate ?></p>
                    <p>Moved to Pending on: <?php echo $pendate ?></p>
                    <p>Moved to Pending by: <?php echo $penby ?></p>
                    <p>Moved to Ongoing on: <?php echo $inpdate ?></p>
                    <p>Moved to Ongoing by: <?php echo $inpby ?></p>
                    <!-- <p>Moved to Completed on: <?php //echo $findate ?></p> -->
                    <!-- <p>Moved to Completed by: <?php //echo $finby ?></p> -->
                    <p>Service Type: <?php echo $serv_type ?></p>
                    <p>Service Description: <?php  echo $servdesc?></p>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                    <button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>
                </div>
            </div>
        </form>
    </div>
</div>