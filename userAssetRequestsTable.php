<tr>
<td><?php echo $uid ?></td>
<td><?php echo $custname ?></td>
<td><?php echo $postdate ?></td>
<td><?php echo $compdate ?></td>
<td><?php echo $status ?></td>
<td>
<a href="#view<?php echo $id ?>" data-toggle="modal"><button type="button" class="btn btn-primary btn-sm">View</button></a>
<?php
if($status == 'inprogress'){
    echo "<a href='#edit" . $id ."' data-toggle='modal'><button type='button' class='btn btn-success btn-sm'>Mark as Completed</button></a>";
}
?>
</td>
<?php
    include('../viewDetailsModal.php');
    include('../markCompletedModal.php');
?>
</tr>