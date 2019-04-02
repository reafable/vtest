<div class="modal fade" id="reject<?php echo $id ?>" tabindex="-1" role="dialog" aria-labelledby="reject<?php echo $id ?>Label" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <form method="post">
        
            <div class="modal-content">
            
                <div class="modal-header">
            
                    <h5 class="modal-title" id="rejectModalLabel">Reject Request</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">$times;</span></button>
            
                </div>
            
            </div>
                <div class="modal-body">
            
                    <input type="hidden" name="reject_req_id" value="<?php echo $id ?>">
                    <p>Are you sure you want to reject this request?</p>
            
            </div>
                <div class="modal-footer">
            
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger" name="reject">Cancel</button>
            
                </div>
        
        </form>

    </div>

</div>