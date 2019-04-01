<tr>
    <td>
        <?php echo $fname; ?>
    </td>
    <td>
        <?php echo $lname; ?>
    </td> 
    <td>
        <?php echo $username; ?>
    </td> 
    <td>
        <?php echo $user_type; ?>
    </td> 
    <td>
        <?php echo $status; ?>
    </td>
    <td>
        <?php if($user_type == 'user'){
        echo
        "<a href='#toggle" . $id . "' data-toggle='modal'> <button type='button' class='btn btn-warning btn-sm'>Toggle Status</button></a>" .
        " " .
        /*"<a href='#toggleType" . $id . "' data-toggle='modal'><button type='button' class='btn btn-danger btn-sm'>Toggle Type</button></a>" .
        " " .*/
        "<a href='#changePassword" . $id . "' data-toggle='modal'><button type='button' class='btn btn-info btn-sm'>Change Password</button></a>" .
        " ";
        } ?>
        <!-- <a href='#edit<?php /*echo $id*/ ?>' data-toggle='modal'><button type='button' class='btn btn-primary btn-sm'>View</button></a> -->
    </td>
    <div class='modal fade' id='toggle<?php echo $id ?>' tabindex='-1' role='dialog' aria-labelledby='reject<?php echo $id ?>Label' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
            <form method='post'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='rejectModalLabel'>Toggle User Status</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <input type='hidden' name='toggle_req_id' value='<?php echo $id ?>'>
                        <p>Are you sure you want to toggle
                            <?php echo $username ?>'s status?</p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                        <button type='submit' class='btn btn-warning' name='toggle'>Toggle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='modal fade' id='toggleType<?php echo $id ?>' tabindex='-1' role='dialog' aria-labelledby='reject<?php echo $id ?>Label' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
            <form method='post'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id=' rejectModalLabel'>Toggle User Type</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <input type='hidden' name='toggle_req_id' value='<?php echo $id?>'>
                        <p>Are you sure you want to toggle <?php echo $username?>'s type?</p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                        <button type='submit' class='btn btn-danger' name='toggleType'>Toggle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='modal fade' id='changePassword<?php echo $id ?>' tabindex='-1' role='dialog' aria-labelledby='reject<?php echo $id ?>Label' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
            <form method='post'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='rejectModalLabel'>Change Password</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <input type='hidden' name='changepassword_req_id' value='<?php echo $id?>'>
                        <!-- <p>Current Password: <?php echo $password ?></p> -->
                        <div class='form-group'>
                            <label for='password3'>New Password</label>
                            <input type='password' class='form-control' id='password3' name='password3' placeholder='Enter new password' required>
                        </div>
                        <div class='form-group'>
                            <label for='password4'>Confirm Password</label>
                            <input type='password' class='form-control' id='password4' name='password4' placeholder='Re-enter  new password' required>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>
                        <button type='submit' class='btn btn-info' name='changePassword'>Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class='modal fade' id='edit<?php echo $id?>' tabindex='-1' role='dialog' aria-labelledby='edit<?php echo $id?>Label' aria-hidden='true'>
        <div class='modal-dialog' role='document'>
            <form method='post'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='editModalLabel'>User Details</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body'>
                        <input type='hidden' name='edit_req_id' value='<?php echo $id?>'>
                        <p>First Name: <?php echo $fname ?></p>
                        <p>Last Name: <?php echo $lname ?></p>
                        <p>Username: <?php echo $username ?></p>
                        <p>User Type: <?php echo $user_type ?></p>
                        <p>Status: <?php echo $status ?></p>
                        <p>Password: <?php echo $password ?></p>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>"
                        <!-- <button type='submit' class='btn btn-primary' name='save'>Save Changes</button> -->"
                    </div>
                </div>
            </form>
        </div>
    </div>
</tr>
