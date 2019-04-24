<?php 
session_start();

// connect to database
$db = mysqli_connect('localhost', 'root', '', 'vtest');

// variable declaration
$username = "";
$email    = "";
$errors   = array();
$custname = "";
$compdate = "";
$servicetext = "";

date_default_timezone_set('Asia/Manila');
$date = date('d M Y h:i:s A');

//
if(isset($_POST['reject'])){
    rejectRequest();
    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['mback'])){
    superMoveRequestToInProgress();
    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['mbacktopool'])){
    superMoveRequestToPool();
    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['mpending'])){
    moveRequestToPending();
    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['minprogress'])){
    moveRequestToInProgress();
    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['mcompleted'])){
    moveRequestToCompleted();
    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['createAsset'])){
    addAsset();
    header('location: ' . $_SERVER['PHP_SELF']);
}

//ongoing pls finish me
function addAsset(){
    global $db, $assetName, $assetDescription;
    
    //$addAssetReq = $_POST['addAssetReq'];
    
    $name = $_POST['name'];
    $description = $_POST['description'];
    
    $query = "INSERT INTO assets 
    (id, name, description) 
    VALUES 
    ('NULL', '$name', '$description')";
    
    mysqli_query($db, $query);
}

//
function rejectRequest(){
    global $db;
    
    $reject_req_id = $_POST['reject_req_id'];
    
    $query = "UPDATE requests
              SET
              status='rejected'
              WHERE
              id='$reject_req_id'";
    mysqli_query($db, $query);
}

function superMoveRequestToInProgress(){
    global $db;
    $move_req_id = $_POST['move_req_id'];
    $query = "UPDATE requests SET status='inprogress', finby='NULL', findate=NULL 
              WHERE 
              id='$move_req_id'";
    mysqli_query($db, $query);
}

function superMoveRequestToPool(){
    global $db;
    $move_req_id = $_POST['move_req_id'];
    $query = "UPDATE requests SET status='pending', finby='NULL', findate=NULL 
              WHERE 
              id='$move_req_id'";
    mysqli_query($db, $query);
}

function moveRequestToPending(){
    global $db;
    
    $edit_req_id = $_POST['edit_req_id'];
    
    $date = date('Y-m-d');
    
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
    }
    
    $query = "UPDATE requests SET status='pending', pendate='$date', penby='$user' WHERE id='$edit_req_id'";
    mysqli_query($db, $query);
}

function moveRequestToInProgress(){
    global $db;
    
    $edit_req_id = $_POST['edit_req_id'];
    
    $targetDate = date('Y-m-d', strtotime($_POST['targetDate']));

    $date = date('Y-m-d');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
    }
    
    $query = "UPDATE requests SET status='inprogress', actdate='$targetDate', inpdate='$date', inpby='$user' WHERE id='$edit_req_id'";
    mysqli_query($db, $query);
}

function moveRequestToCompleted(){
    global $db;
    
    $edit_req_id = $_POST['edit_req_id'];
    
    $date = date('Y-m-d');

    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
    }
    
    $query = "UPDATE requests
              SET
              status='completed', 
              findate='$date', 
              finby='$user'
              WHERE
              id='$edit_req_id'";
    mysqli_query($db, $query);
}

// call postServiceRequest() function when submitsr is clicked
if(isset($_POST['submitsr'])){
    postServiceRequest();
}

// create service request
function postServiceRequest(){
    global $db, $custname, $compdate, $servdesc;
    
    $date = date('Y-m-d');
    
    //$date = date('m-d-Y');
    
    $uid = date('Ymd-His');
    
    $_SESSION['uid'] = "SR-" . $uid;
    
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
        $branch = $_SESSION['user']['branch'];
        $department = $_SESSION['user']['department'];
    }
    
    $custname = e($_POST['custname']);
    $postdate = $date;
    $compdate = e($_POST['compdate']);
    $servdesc = e($_POST['servdesc']);
    $serv_type = e($_POST['serv_type']);
    
    $query = "INSERT INTO requests 
              (id, uid, type, custname, postdate, compdate, servdesc, serv_type, assetdesc, postby, status, branch, department) 
              VALUES 
              (NULL, '" . $_SESSION['uid'] . "', 'service', '$custname', '$postdate', '$compdate', '$servdesc', '$serv_type', 'N/A', '$user', 'pooled', '$branch', '$department')";
    
    mysqli_query($db, $query);
    header('location: ../requests/confirmation.php');
}

// call postAssetRequest() function when submitar is clicked
if(isset($_POST['submitar'])){
    postAssetRequest();
}

//create asset request
function postAssetRequest(){
    global $db, $custname, $compdate, $assetdesc;
    
    $date = date('Y-m-d');
    
    $uid = date('Ymd-His');
    
    $_SESSION['uid'] = "AR-" . $uid;
    
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
        $username = $_SESSION['user']['user'];
        $department = $_SESSION['user']['department'];
        $branch = $_SESSION['user']['branch'];
    }
    
    $custname = e($_POST['custname']);
    $postdate = $date;
    $compdate = e($_POST['compdate']);
    $assetdesc = $_POST['assetdesc'];
    $quantity = $_POST['assetQuantity'];
    
    foreach($assetdesc as $descVal => $a){
        
        $insertVal[] = $a . " x " . $quantity[$descVal];
    }
    
    $query = "INSERT INTO requests 
              (id, uid, type, custname, postdate, compdate, servdesc, serv_type, assetdesc, postby, status, branch, department) 
              VALUES 
              (NULL, '" . $_SESSION['uid'] . "', 'asset', '$custname', '$postdate', '$compdate', 'N/A', 'N/A', '" . implode(', ', $insertVal) . "', '$user', 'pooled', '$branch', '$department')";
    mysqli_query($db, $query);
    header('location: ../requests/confirmation.php');
}

// call the register() function if register_btn is clicked
if (isset($_POST['register_btn'])) {
	register();
}

if(isset($_POST['createUser'])) {
	createUser();
    header('location: ' . $_SERVER['PHP_SELF']);
}

function createUser(){
    global $db;
    
    $username = e($_POST['username']);
    $password1 = md5(e($_POST['password1']));
    $password2 = md5(e($_POST['password2']));
    $user_type = e($_POST['user_type']);
    //$status = e($_POST['status']);
    $fname = e($_POST['fname']);
    $lname = e($_POST['lname']);
    $branch = e($_POST['branch']);
    $department = e($_POST['department']);

    if($password1 == $password2){
        $password = $password1;
        $query = "INSERT INTO users (id, username, password, user_type, status, fname, lname, branch, department) VALUES ('NULL', '$username', '$password', '$user_type', 'active', '$fname', '$lname', '$branch', '$department')";
        mysqli_query($db, $query);
    }
}

// user registration
function register(){
	// call variables with the global keyword to make them available in function
	global $db, $errors, $username, $email;

	// grab input values from form; call the e() function
    // defined below to escape form values
	$username    =  e($_POST['username']);
	$email       =  e($_POST['email']);
	$password_1  =  e($_POST['password_1']);
	$password_2  =  e($_POST['password_2']);

	// form validation
	if (empty($username)) { 
		array_push($errors, "Username is required"); 
	}
	if (empty($email)) { 
		array_push($errors, "Email is required"); 
	}
	if (empty($password_1)) { 
		array_push($errors, "Password is required"); 
	}
	if ($password_1 != $password_2) {
		array_push($errors, "The two passwords do not match");
	}

	// register user if there are no errors in the form
	if (count($errors) == 0) {
		$password = md5($password_1);//encrypt the password before saving in the database

		if (isset($_POST['user_type'])) {
			$user_type = e($_POST['user_type']);
			$query = "INSERT INTO users (username, email, user_type, password) 
					  VALUES('$username', '$email', '$user_type', '$password')";
			mysqli_query($db, $query);
			$_SESSION['success']  = "New user successfully created!!";
			header('location: home.php');
		}else{
			$query = "INSERT INTO users (username, email, user_type, password) 
					  VALUES('$username', '$email', 'user', '$password')";
			mysqli_query($db, $query);

			// get id of the created user
			$logged_in_user_id = mysqli_insert_id($db);

			$_SESSION['user'] = getUserById($logged_in_user_id); // put logged in user in session
			$_SESSION['success']  = "You are now logged in";
			header('location: login.php');				
		}
	}
}

// return user array from their id
function getUserById($id){
	global $db;
	$query = "SELECT * FROM users WHERE id=" . $id;
	$result = mysqli_query($db, $query);

	$user = mysqli_fetch_assoc($result);
	return $user;
}

// escape string
function e($val){
	global $db;
	return mysqli_real_escape_string($db, trim($val));
}

function display_error() {
	global $errors;

	if (count($errors) > 0){
		echo '<div class="error">';
			foreach ($errors as $error){
				echo $error .'<br>';
			}
		echo '</div>';
	}
}

function isLoggedIn()
{
	if (isset($_SESSION['user'])) {
		return true;
	}else{
		return false;
	}
}

// call the login() function if login is clicked
if (isset($_POST['login'])) {
	login();
}

if (isset($_POST['loginc'])) {
	loginC();
}

// log user out if logout is clicked
if (isset($_GET['logout'])) {
	session_destroy();
	unset($_SESSION['user']);
	header("location: login.php");
}

// user login
function login(){
	global $db, $username, $errors;

	// grab form values
	$username = e($_POST['username']);
	$password = e(md5($_POST['password']));

	// check if form is complete
	if (empty($username)) {
		array_push($errors, "Username is required");
	}
	if (empty($password)) {
		array_push($errors, "Password is required");
	}

	// attempt login
	if (count($errors) == 0) {
		$password = $password;

		$query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND status='active' LIMIT 1";
		$results = mysqli_query($db, $query);

		if (mysqli_num_rows($results) == 1) { // user found
			// check if user is admin or not
			$logged_in_user = mysqli_fetch_assoc($results);
			if ($logged_in_user['user_type'] == 'sadmin') {

				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "yay";
				header('location: admin/home.php');		  
			}
            else if ($logged_in_user['user_type'] == 'admin') {

				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "yay";
				header('location: admin/home.php');		  
			}else{
				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "yay";

				header('location: requests/home.php');
			}
        }else{
            echo 
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Your username or password may be wrong. Or your account is inactive.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
	}
}

function loginC(){
	global $db, $username, $errors;

	// grab form values
	$username = e($_POST['username']);
	$password = e(md5($_POST['password']));

	// check if form is complete
	if (empty($username)) {
		array_push($errors, "Username is required");
	}
	if (empty($password)) {
		array_push($errors, "Password is required");
	}

	// attempt login
	if (count($errors) == 0) {
		$password = $password;

		$query = "SELECT * FROM customers WHERE username='$username' AND password='$password' AND status='active' LIMIT 1";
		$results = mysqli_query($db, $query);

		if (mysqli_num_rows($results) == 1) { // user found
			// check if user is admin or not
			$logged_in_user = mysqli_fetch_assoc($results);
			if ($logged_in_user['user_type'] == 'customer') {

				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "yay";
				header('location: customer.php');		  
			}
		}else{
            echo 
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                Your username or password may be wrong. Or your account is inactive.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
        }
	}
}

function isAdmin(){
	if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'sadmin') {
		return true;
	}
    else if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'admin'){
        return true;
    }else{
		return false;
	}
}

function displayRequests(){
    global $db;
    $query = "SELECT uid, type, custname, postdate, compdate, postby, status from requests";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            
            $uid = $row['uid'];
            $type = $row['type'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $compdate = $row['compdate'];
            $postby = $row['postby'];
            $status = $row['status'];
            
            echo
                "<tr>" .
                "<td>" . $uid . "</td>" .
                "<td>" . $type . "</td>" .
                "<td>" . $custname . "</td>" .
                "<td>" . $postdate . "</td>" .
                //"<td>" . $compdate . "</td>" .
                "<td>" . $postby . "</td>" .
                "<td>" . $status . "</td>" .
                "</tr>";
            
        }
    }
}

if(isset($_POST['toggle'])){
    
    toggleUserStatus();

    header('location: ' . $_SERVER['PHP_SELF']);
}

if(isset($_POST['toggleType'])){
    
    toggleUserType();

    header('location: ' . $_SERVER['PHP_SELF']);
}

function toggleUserStatus(){
    global $db;
    
    $toggle_req_id = $_POST['toggle_req_id'];
    
    $query = "SELECT status FROM users WHERE id='$toggle_req_id'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results))
            
            $status = $row['status'];
        
        if($status == "active"){
            $query = "UPDATE users SET status='inactive' WHERE id='$toggle_req_id'";
            mysqli_query($db, $query);
        }
        if($status == "inactive"){
            $query = "UPDATE users SET status='active' WHERE id='$toggle_req_id'";
            mysqli_query($db, $query);
        }
    }
}

function toggleUserType(){
    global $db;
    
    $toggle_req_id = $_POST['toggle_req_id'];
    
    $query = "SELECT user_type FROM users WHERE id='$toggle_req_id'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results))
            
            $user_type = $row['user_type'];
        
        if($user_type == "admin"){
            $query = "UPDATE users SET user_type='user' WHERE id='$toggle_req_id'";
            mysqli_query($db, $query);
        }
        if($user_type == "user"){
            $query = "UPDATE users SET user_type='admin' WHERE id='$toggle_req_id'";
            mysqli_query($db, $query);
        }
    }
}

if(isset($_POST['toggle'])){

    //addAssetStock();

    header('location: ' . $_SERVER['PHP_SELF']);
}


/*function addAssetStock(){
    global $db;
    
    $toggle_req_id = $_POST['toggle_req_id'];
    
    $query = "SELECT stock FROM assets WHERE id='$toggle_req_id'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results))
            
            $stock = $row['stock'];
        
        if($status == "active"){
            $query = "UPDATE users SET status='inactive' WHERE id='$toggle_req_id'";
            mysqli_query($db, $query);
        }
        if($status == "inactive"){
            $query = "UPDATE users SET status='active' WHERE id='$toggle_req_id'";
            mysqli_query($db, $query);
        }
    }
}*/

//pull data from users table
function displayUsers(){
    global $db;
    $query = "SELECT id, status, username, fname, lname, password, user_type, branch, department FROM users";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            
            $id = $row['id'];
            $fname = $row['fname'];
            $lname = $row['lname'];
            $username = $row['username'];
            $user_type = $row['user_type'];
            $status = $row['status'];
            $password = $row['password'];
            $branch = $row['branch'];
            $department = $row['department'];
            
            if($_SESSION['user']['user_type'] == 'sadmin'){
                
                include('../sadminUsersTable.php');
                /*echo
                    "<tr>" .
                    //"<td>" . $row['id'] . "</td>" . 
                    "<td>" . $fname . "</td>" .
                    "<td>" . $lname . "</td>" .
                    "<td>" . $username . "</td>" .
                    "<td>" . $user_type . "</td>" .
                    "<td>" . $status . "</td>" .
                    //"<td>" . $row['password'] . "</td>" .
                    "<td>" . 
                    "<a href='#toggle" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-warning btn-sm'>Toggle Status</button></a>" . 
                    " " . 
                    "<a href='#toggleType" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Toggle Type</button></a>" . 
                    " " . 
                    "<a href='#changePassword" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-info btn-sm'>Change Password</button></a>" . 
                    " " .
                    "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                    "</td>".
                    "<div class='modal fade' id='toggle" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title' id='rejectModalLabel'>Toggle User Status</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='toggle_req_id' value='" . $id . "'>" .
                    "<p>Are you sure you want to toggle ". $username ."'s status?</p>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                    "<button type='submit' class='btn btn-warning' name='toggle'>Toggle</button>" .
                    "</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal fade' id='toggleType" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title' id='rejectModalLabel'>Toggle User Type</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='toggle_req_id' value='" . $id . "'>" .
                    "<p>Are you sure you want to toggle ". $username ."'s type?</p>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                    "<button type='submit' class='btn btn-danger' name='toggleType'>Toggle</button>" .
                    "</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal fade' id='changePassword" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title' id='rejectModalLabel'>Change Password</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='changepassword_req_id' value='" . $id . "'>" .
                    "<p>Current Password: " . $password . "</p>" .
                    "<div class='form-group'>" .
                    "<label for='password3'>New Password</label>" .
                    "<input type='password' class='form-control' id='password3' name='password3' placeholder='Enter new password' required>" .
                    "</div>" .
                    "<div class='form-group'>" .
                    "<label for='password4'>Confirm Password</label>" .
                    "<input type='password' class='form-control' id='password4' name='password4' placeholder='Re-enter  new password' required>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                    "<button type='submit' class='btn btn-info' name='changePassword'>Save</button>" .
                    "</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title id='editModalLabel'>User Details</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                    "<p>First Name: " . $fname . "</p>" .
                    "<p>Last Name: " . $lname . "</p>" .
                    "<p>Username: " . $username . "</p>" .
                    "<p>User Type: " . $user_type . "</p>" .
                    "<p>Status: " . $status . "</p>" .
                    "<p>Password: " . $password . "</p>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>" ./*
                                                    "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .*/
                    /*"</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "</tr>";  */
                
                
            }else{
                
                
                include('../adminUsersTable.php');
                
                /*echo
                    "<tr>" .
                    //"<td>" . $row['id'] . "</td>" . 
                    "<td>" . $fname. "</td>" .
                    "<td>" . $lname . "</td>" .
                    "<td>" . $username . "</td>" .
                    "<td>" . $user_type . "</td>" .
                    "<td>" . $status . "</td>" .
                    //"<td>" . $row['password'] . "</td>" .
                    "<td>" . /*
                    "<a href='#toggle" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-warning btn-sm'>Toggle Status</button></a>" . 
                    " " . 
                    "<a href='#toggleType" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Toggle Type</button></a>" . *//*
                    " " . 
                    "<a href='#changePassword" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-info btn-sm'>Change Password</button></a>" . 
                    " " .
                    "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                    "</td>".
                    "<div class='modal fade' id='toggle" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title id='rejectModalLabel'>Toggle User Status</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='toggle_req_id' value='" . $id . "'>" .
                    "<p>Are you sure you want to toggle ". $username ."'s status?</p>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                    "<button type='submit' class='btn btn-warning' name='toggle'>Toggle</button>" .
                    "</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal fade' id='toggleType" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title id='rejectModalLabel'>Toggle User Type</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='toggle_req_id' value='" . $id . "'>" .
                    "<p>Are you sure you want to toggle ". $username ."'s type?</p>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                    "<button type='submit' class='btn btn-danger' name='toggleType'>Toggle</button>" .
                    "</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal fade' id='changePassword" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title id='rejectModalLabel'>Change Password</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='changepassword_req_id' value='" . $id . "'>" .
                    "<p>Current Password: " . $password . "</p>" .
                    "<div class='form-group'>" .
                    "<label for='password3'>New Password</label>" .
                    "<input type='password' class='form-control' id='password3' name='password3' placeholder='Enter new password' required>" .
                    "</div>" .
                    "<div class='form-group'>" .
                    "<label for='password4'>Confirm Password</label>" .
                    "<input type='password' class='form-control' id='password4' name='password4' placeholder='Re-enter  new password' required>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                    "<button type='submit' class='btn btn-info' name='changePassword'>Save</button>" .
                    "</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                    "<form method='post'>" .
                    "<div class='modal-content'>" .
                    "<div class='modal-header'>" .
                    "<h5 class='modal-title id='editModalLabel'>User Details</h5>" .
                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                    "<span aria-hidden='true'>&times;</span>" .
                    "</button>" .
                    "</div>" .
                    "<div class='modal-body'>" .
                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                    "<p>First Name: " . $fname . "</p>" .
                    "<p>Last Name: " . $lname . "</p>" .
                    "<p>Username: " . $username . "</p>" .
                    "<p>User Type: " . $user_type . "</p>" .
                    "<p>Status: " . $status . "</p>" .
                    "<p>Password: " . $password . "</p>" .
                    "</div>" .
                    "<div class='modal-footer'>" .
                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>" ./*
                                                    "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .*/
                    /*"</div>" .
                    "</div>" .
                    "</form>" .
                    "</div>" .
                    "</div>" .
                    "</tr>";*/
                
                
                
                
                
            }
            
            
        }
    }
}

if(isset($_POST['changePassword'])){
    
    changePassword();
    header('location: ' . $_SERVER['PHP_SELF']);
    
}

function changePassword(){

    global $db;
    
    $changepassword_req_id = $_POST['changepassword_req_id'];

    $password3 = e($_POST['password3']);
    $password4 = e($_POST['password4']);

    if($password3 == $password4){

        $query = "UPDATE users SET password='" . md5($password3) . "' WHERE id='$changepassword_req_id'";
        mysqli_query($db, $query);

    }

}

function displayUserServiceRequests(){
    global $db;
    $user = $_SESSION['user']['username'];
    $query = "SELECT * FROM requests WHERE postby='$user' AND type='service'";
    $results = mysqli_query($db, $query);

    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $uid = $row['uid'];
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $actdate = $row['actdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $status = $row['status'];
            $serv_type = $row['serv_type'];
            $servdesc = $row['servdesc'];
            $pendate = $row['pendate'];
            $inpdate = $row['inpdate'];
            $findate = $row['findate'];
            $finby = $row['finby'];
            $inpby = $row['inpby'];
            $penby = $row['penby'];
            $branch = $row['branch'];
            $department = $row['department'];
            include('userServiceRequestsTable.php');
        }
    }
}

function displayUserAssetRequests(){
    global $db;
    $user = $_SESSION['user']['username'];
    $query = "SELECT * FROM requests WHERE postby='$user' AND type='asset'";
    $results = mysqli_query($db, $query);

    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $uid = $row['uid'];
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $actdate = $row['actdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $status = $row['status'];
            $assetdesc = $row['servdesc'];
            $pendate = $row['pendate'];
            $inpdate = $row['inpdate'];
            $findate = $row['findate'];
            $finby = $row['finby'];
            $inpby = $row['inpby'];
            $penby = $row['penby'];
            $branch = $row['branch'];
            $department = $row['department'];
            include('userAssetRequestsTable.php');
        }
    }
}

// pull pooled service requests from requests table
function displayServicePooled(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, serv_type, servdesc, postby FROM requests WHERE type='service' AND status='pooled'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $serv_type = $row['serv_type'];
            $servdesc = $row['servdesc'];

            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo
            "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                    // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                    // " "    .
                    "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                                "<div class='modal-header'>" .
                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                        "<span aria-hidden='true'>&times;</span>" .
                                    "</button>" .
                                "</div>" .
                                "<div class='modal-body'>" .
                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                    "<p>Are you sure you want to reject this request?</p>" .
                                "</div>" .
                                "<div class='modal-footer'>" .
                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                "</div>" .
                            "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                                "<div class='modal-header'>" .
                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                            "<span aria-hidden='true'>&times;</span>" .
                                        "</button>" .
                                "</div>" .
                                "<div class='modal-body'>" .
                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                    "<p>Customer Name: " . $custname . "</p>" .
                                    "<p>Posted On: " . $postdate . "</p>" .
                                    "<p>Posted By: " . $postby . "</p>" .
                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                    "<p>Service Type: " . $serv_type . "</p>" .
                                    "<p>Service Description: " . $servdesc . "</p>" .
                                "</div>" .
                                "<div class='modal-footer'>" .
                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                    "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
                                "</div>" .
                            "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" .
            "</tr>";
            }else{
                echo
            "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                    // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                    // " "    .
                    "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                                "<div class='modal-header'>" .
                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                        "<span aria-hidden='true'>&times;</span>" .
                                    "</button>" .
                                "</div>" .
                                "<div class='modal-body'>" .
                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                    "<p>Are you sure you want to reject this request?</p>" .
                                "</div>" .
                                "<div class='modal-footer'>" .
                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                "</div>" .
                            "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                                "<div class='modal-header'>" .
                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                            "<span aria-hidden='true'>&times;</span>" .
                                        "</button>" .
                                "</div>" .
                                "<div class='modal-body'>" .
                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                    "<p>Customer Name: " . $custname . "</p>" .
                                    "<p>Posted On: " . $postdate . "</p>" .
                                    "<p>Posted By: " . $postby . "</p>" .
                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                    "<p>Service Type: " . $serv_type . "</p>" .
                                    "<p>Service Description: " . $servdesc . "</p>" .
                                "</div>" .
                                "<div class='modal-footer'>" .
                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                    "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
                                "</div>" .
                            "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" .
            "</tr>";
            }

        // echo
        //     "<tr>" .
        //         "<td>" . $custname . "</td>".
        //         "<td>" . $postdate . "</td>".
        //         "<td>" . $postby . "</td>".
        //         "<td>" . $compdate . "</td>".
        //         "<td>" . $serv_type . "</td>".
        //         "<td>" . $servdesc . "</td>".
        //         "<td>" . 
        //             "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
        //             " "    .
        //             "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
        //         "</td>" .
        //         "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
        //             "<div class='modal-dialog' role='document'>" .
        //                 "<form method='post'>" .
        //                     "<div class='modal-content'>" .
        //                         "<div class='modal-header'>" .
        //                             "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
        //                             "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
        //                                 "<span aria-hidden='true'>&times;</span>" .
        //                             "</button>" .
        //                         "</div>" .
        //                         "<div class='modal-body'>" .
        //                             "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
        //                             "<p>Are you sure you want to reject this request?</p>" .
        //                         "</div>" .
        //                         "<div class='modal-footer'>" .
        //                             "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
        //                             "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
        //                         "</div>" .
        //                     "</div>" .
        //                 "</form>" .
        //             "</div>" .
        //         "</div>" .
        //         "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
        //             "<div class='modal-dialog' role='document'>" .
        //                 "<form method='post'>" .
        //                     "<div class='modal-content'>" .
        //                         "<div class='modal-header'>" .
        //                             "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
        //                                 "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
        //                                     "<span aria-hidden='true'>&times;</span>" .
        //                                 "</button>" .
        //                         "</div>" .
        //                         "<div class='modal-body'>" .
        //                             "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
        //                             "<p>Customer Name: " . $custname . "</p>" .
        //                             "<p>Posted On: " . $postdate . "</p>" .
        //                             "<p>Posted By: " . $postby . "</p>" .
        //                             "<p>Expected Completion: " . $compdate . "</p>" .
        //                             "<p>Service Type: " . $serv_type . "</p>" .
        //                             "<p>Service Description: " . $servdesc . "</p>" .
        //                         "</div>" .
        //                         "<div class='modal-footer'>" .
        //                             "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
        //                             "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
        //                         "</div>" .
        //                     "</div>" .
        //                 "</form>" .
        //             "</div>" .
        //         "</div>" .
        //     "</tr>";
        }
    }
}

function displayAssetPooled(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby FROM requests WHERE type='asset' AND status='pooled'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $assetdesc = $row['assetdesc'];

            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo
            "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                    // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                    // " "    .
                    "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                            "<div class='modal-header'>" .
                                "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                    "<span aria-hidden='true'>&times;</span>" .
                                "</button>" .
                            "</div>" .
                            "<div class='modal-body'>" .
                                "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                "<p>Are you sure you want to reject this request?</p>" .
                            "</div>" .
                            "<div class='modal-footer'>" .
                                "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                            "</div>" .
                        "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                            "<div class='modal-header'>" .
                                "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                    "<span aria-hidden='true'>&times;</span>" .
                                "</button>" .
                            "</div>" .
                            "<div class='modal-body'>" .
                                "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                "<p>Customer Name: " . $custname . "</p>" .
                                "<p>Posted On: " . $postdate . "</p>" .
                                "<p>Posted By: " . $postby . "</p>" .
                                "<p>Expected Completion: " . $compdate . "</p>" .
                                "<p>Assets Required: " . $assetdesc . "</p>" .
                            "</div>" .
                            "<div class='modal-footer'>" .
                                "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
                            "</div>" .
                        "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" . 
            "</tr>";
            }else{
                echo
            "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                    // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                    // " "    .
                    "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                            "<div class='modal-header'>" .
                                "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                    "<span aria-hidden='true'>&times;</span>" .
                                "</button>" .
                            "</div>" .
                            "<div class='modal-body'>" .
                                "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                "<p>Are you sure you want to reject this request?</p>" .
                            "</div>" .
                            "<div class='modal-footer'>" .
                                "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                            "</div>" .
                        "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                    "<div class='modal-dialog' role='document'>" .
                        "<form method='post'>" .
                            "<div class='modal-content'>" .
                            "<div class='modal-header'>" .
                                "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                    "<span aria-hidden='true'>&times;</span>" .
                                "</button>" .
                            "</div>" .
                            "<div class='modal-body'>" .
                                "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                "<p>Customer Name: " . $custname . "</p>" .
                                "<p>Posted On: " . $postdate . "</p>" .
                                "<p>Posted By: " . $postby . "</p>" .
                                "<p>Expected Completion: " . $compdate . "</p>" .
                                "<p>Assets Required: " . $assetdesc . "</p>" .
                            "</div>" .
                            "<div class='modal-footer'>" .
                                "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
                            "</div>" .
                        "</div>" .
                        "</form>" .
                    "</div>" .
                "</div>" . 
            "</tr>";
            }

        // echo
        //     "<tr>" .
        //         "<td>" . $custname . "</td>".
        //         "<td>" . $postdate . "</td>".
        //         "<td>" . $postby . "</td>".
        //         "<td>" . $compdate . "</td>".
        //         "<td>" . $assetdesc . "</td>".
        //         "<td>" . 
        //             "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
        //             " "    .
        //             "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
        //         "</td>" .
        //         "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
        //             "<div class='modal-dialog' role='document'>" .
        //                 "<form method='post'>" .
        //                     "<div class='modal-content'>" .
        //                     "<div class='modal-header'>" .
        //                         "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
        //                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
        //                             "<span aria-hidden='true'>&times;</span>" .
        //                         "</button>" .
        //                     "</div>" .
        //                     "<div class='modal-body'>" .
        //                         "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
        //                         "<p>Are you sure you want to reject this request?</p>" .
        //                     "</div>" .
        //                     "<div class='modal-footer'>" .
        //                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
        //                         "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
        //                     "</div>" .
        //                 "</div>" .
        //                 "</form>" .
        //             "</div>" .
        //         "</div>" .
        //         "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
        //             "<div class='modal-dialog' role='document'>" .
        //                 "<form method='post'>" .
        //                     "<div class='modal-content'>" .
        //                     "<div class='modal-header'>" .
        //                         "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
        //                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
        //                             "<span aria-hidden='true'>&times;</span>" .
        //                         "</button>" .
        //                     "</div>" .
        //                     "<div class='modal-body'>" .
        //                         "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
        //                         "<p>Customer Name: " . $custname . "</p>" .
        //                         "<p>Posted On: " . $postdate . "</p>" .
        //                         "<p>Posted By: " . $postby . "</p>" .
        //                         "<p>Expected Completion: " . $compdate . "</p>" .
        //                         "<p>Assets Required: " . $assetdesc . "</p>" .
        //                     "</div>" .
        //                     "<div class='modal-footer'>" .
        //                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
        //                         "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
        //                     "</div>" .
        //                 "</div>" .
        //                 "</form>" .
        //             "</div>" .
        //         "</div>" . 
        //     "</tr>";
        }
    }
}

function displayServicePending(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, serv_type, servdesc, postby, pendate, penby FROM requests WHERE type='service' AND status='pending'";
    $results = mysqli_query($db, $query);
    
    /*if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            echo "<tr><td>" .$row['custname'] . "</td><td>" . $row['postdate'] . "</td><td>" . $row['postby'] . "</td><td>" . $row['compdate'] . "</td><td>" . $row['servdesc'] . "</td><td>" . "<a href='#reject" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'><i class='far times-circle'></i></button></a>" . "<a href='#edit" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'><i class='far times-circle'></i></button></a>" . "</td></tr>";
        }
    }*/
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $pendate = $row['pendate'];
            $penby = $row['penby'];
            $serv_type = $row['serv_type'];
            $servdesc = $row['servdesc'];

            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Moved To Pending On: " . $pendate . "</p>" .
                                                    "<p>Moved To Pending By: " . $penby . "</p>" .
                                                    "<p>Service Type: " . $serv_type . "</p>" .
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                "<label for='targetDate'>Target Completion Date</label>" .
                "<input type='date' class='form-control' id='targetDateService' name='targetDate' placeholder='Enter date' required>" .
                                                    //insert form here: target completion date
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    "</tr>";
            }else{
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $pendate . "</td>".
                // "<td>" . $penby . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                // " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Moved To Pending On: " . $pendate . "</p>" .
                                                    "<p>Moved To Pending By: " . $penby . "</p>" .
                                                    "<p>Service Type: " . $serv_type . "</p>" .
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                "<label for='targetDate'>Target Completion Date</label>" .
                "<input type='date' class='form-control' id='targetDateService' name='targetDate' placeholder='Enter date' required>" .
                                                    //insert form here: target completion date
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    "</tr>";
            }

            // echo
            //     "<tr>" .
            //     "<td>" . $custname . "</td>".
            //     "<td>" . $postdate . "</td>".
            //     "<td>" . $postby . "</td>".
            //     "<td>" . $compdate . "</td>".
            //     "<td>" . $pendate . "</td>".
            //     "<td>" . $penby . "</td>".
            //     "<td>" . $serv_type . "</td>".
            //     "<td>" . $servdesc . "</td>".
            //     "<td>" . 
            //     "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
            //     " "    .
            //     "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
            //     "</td>" .
            //     "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
            //                             "<div class='modal-dialog' role='document'>" .
            //                                "<form method='post'>" .
            //                                     "<div class='modal-content'>" .
            //                                     "<div class='modal-header'>" .
            //                                         "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
            //                                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
            //                                             "<span aria-hidden='true'>&times;</span>" .
            //                                         "</button>" .
            //                                     "</div>" .
            //                                     "<div class='modal-body'>" .
            //                                         "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
            //                                         "<p>Are you sure you want to reject this request?</p>" .
            //                                     "</div>" .
            //                                     "<div class='modal-footer'>" .
            //                                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
            //                                         "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
            //                                     "</div>" .
            //                                 "</div>" .
            //                                "</form>" .
            //                             "</div>" .
            //                         "</div>" .
            //     "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
            //                             "<div class='modal-dialog' role='document'>" .
            //                                "<form method='post'>" .
            //                                     "<div class='modal-content'>" .
            //                                     "<div class='modal-header'>" .
            //                                         "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
            //                                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
            //                                             "<span aria-hidden='true'>&times;</span>" .
            //                                         "</button>" .
            //                                     "</div>" .
            //                                     "<div class='modal-body'>" .
            //                                         "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
            //                                         "<p>Customer Name: " . $custname . "</p>" .
            //                                         "<p>Posted On: " . $postdate . "</p>" .
            //                                         "<p>Posted By: " . $postby . "</p>" .
            //                                         "<p>Expected Completion: " . $compdate . "</p>" .
            //                                         "<p>Moved To Pending On: " . $pendate . "</p>" .
            //                                         "<p>Moved To Pending By: " . $penby . "</p>" .
            //                                         "<p>Service Type: " . $serv_type . "</p>" .
            //                                         "<p>Service Description: " . $servdesc . "</p>" .
            //     "<label for='targetDate'>Target Completion Date</label>" .
            //     "<input type='date' class='form-control' id='targetDateService' name='targetDate' placeholder='Enter date' required>" .
            //                                         //insert form here: target completion date
            //                                     "</div>" .
            //                                     "<div class='modal-footer'>" .
            //                                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
            //                                         "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
            //                                     "</div>" .
            //                                 "</div>" .
            //                                "</form>" .
            //                             "</div>" .
            //                         "</div>"
            //     ;
        }
    }
}

function displayServiceInprogress(){
    global $db;
    $query = "SELECT * FROM requests WHERE type='service' AND status='inprogress'";
    $results = mysqli_query($db, $query);
    
    /*if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            echo "<tr><td>" .$row['custname'] . "</td><td>" . $row['postdate'] . "</td><td>" . $row['postby'] . "</td><td>" . $row['compdate'] . "</td><td>" . $row['servdesc'] . "</td><td>" . "<a href='#reject" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'><i class='far times-circle'></i></button></a>" . "<a href='#edit" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'><i class='far times-circle'></i></button></a>" . "</td></tr>";
        }
    }*/
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $penby = $row['penby'];
            $inpby = $row['inpby'];
            $compdate = $row['compdate'];
            $actdate = $row['actdate'];
            $pendate = $row['pendate'];
            $inpdate = $row['inpdate'];
            $serv_type = $row['serv_type'];
            $servdesc = $row['servdesc'];
            $branch = $row['branch'];
            $department = $row['department'];

            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $branch . "</td>".
                "<td>" . $department . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $inpdate . "</td>".
                "<td>" . $inpby . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                // " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Service Type: " . $serv_type . "</p>" .
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                "</tr>";
            }else{
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $branch . "</td>".
                "<td>" . $department . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $inpdate . "</td>".
                "<td>" . $inpby . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                // " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Service Type: " . $serv_type . "</p>" .
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    // "<button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                "</tr>";
            }

        }
    }
}

function displayAssetInprogress(){
    global $db;
    $query = "SELECT * FROM requests WHERE type='asset' AND status='inprogress'";
    $results = mysqli_query($db, $query);
    
    /*if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            echo "<tr><td>" .$row['custname'] . "</td><td>" . $row['postdate'] . "</td><td>" . $row['postby'] . "</td><td>" . $row['compdate'] . "</td><td>" . $row['servdesc'] . "</td><td>" . "<a href='#reject" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'><i class='far times-circle'></i></button></a>" . "<a href='#edit" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'><i class='far times-circle'></i></button></a>" . "</td></tr>";
        }
    }*/
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $actdate = $row['actdate'];
            $assetdesc = $row['assetdesc'];
            $pendate = $row['pendate'];
            $penby = $row['penby'];
            $inpdate = $row['inpdate'];
            $inpby = $row['inpby'];
            $branch = $row['branch'];
            $department = $row['department'];

            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $branch . "</td>".
                "<td>" . $department . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $inpdate . "</td>".
                "<td>" . $inpby . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                // " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Service Description: " . $assetdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    "</tr>";
            }else{
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $branch . "</td>".
                "<td>" . $department . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $inpdate . "</td>".
                "<td>" . $inpby . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                // " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Service Description: " . $assetdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    // "<button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    "</tr>";
            }

            
        }
    }
}

function displayServiceCompleted(){
    global $db;
    $query = "SELECT * FROM requests WHERE type='service' AND status='completed'";
    $results = mysqli_query($db, $query);
    
    /*if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            echo "<tr><td>" .$row['custname'] . "</td><td>" . $row['postdate'] . "</td><td>" . $row['postby'] . "</td><td>" . $row['compdate'] . "</td><td>" . $row['servdesc'] . "</td><td>" . "<a href='#reject" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'><i class='far times-circle'></i></button></a>" . "<a href='#edit" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'><i class='far times-circle'></i></button></a>" . "</td></tr>";
        }
    }*/
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $actdate = $row['actdate'];
            $pendate = $row['pendate'];
            $penby = $row['penby'];
            $inpdate = $row['inpdate'];
            $inpby = $row['inpby'];
            $findate = $row['findate'];
            $finby = $row['finby'];
            $serv_type = $row['serv_type'];
            $servdesc = $row['servdesc'];
            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $inpdate . "</td>".
                "<td>" . $inpby . "</td>".
                "<td>" . $findate . "</td>".
                "<td>" . $finby . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                /* "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    . */
                "<a href='#move" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Undo</button></a>" . 
                " " .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                /* "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" . */
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Actual Completion: " . $findate . "</p>" .
                                                    "<p>Service Description: " . $serv_type . "</p>" .
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>" .
                                                    /* "<button type='submit' class='btn btn-primary' name='minprogress'>Mark as In-progress</button>" . */
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" . 
                    include('../moveBackModal.php');
                ;
            }else{
                echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $inpdate . "</td>".
                "<td>" . $inpby . "</td>".
                "<td>" . $findate . "</td>".
                "<td>" . $finby . "</td>".
                "<td>" . $serv_type . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                /* "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    . */
                // "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                /* "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" . */
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Actual Completion: " . $findate . "</p>" .
                                                    "<p>Service Description: " . $serv_type . "</p>" .
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>" .
                                                    /* "<button type='submit' class='btn btn-primary' name='minprogress'>Mark as In-progress</button>" . */
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
            } 
        }
    }
}

function displayAssetCompleted(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, actdate, findate, assetdesc, postby FROM requests WHERE type='asset' AND status='completed'";
    $results = mysqli_query($db, $query);
    
    /*if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            echo "<tr><td>" .$row['custname'] . "</td><td>" . $row['postdate'] . "</td><td>" . $row['postby'] . "</td><td>" . $row['compdate'] . "</td><td>" . $row['servdesc'] . "</td><td>" . "<a href='#reject" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'><i class='far times-circle'></i></button></a>" . "<a href='#edit" . $row['id'] . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'><i class='far times-circle'></i></button></a>" . "</td></tr>";
        }
    }*/
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $actdate = $row['actdate'];
            $findate = $row['findate'];
            $assetdesc = $row['assetdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $actdate . "</td>".
                "<td>" . $findate . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                /* "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    . */
                "<a href='#move" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Undo</button></a>" . 
                " " .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                /* "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" . */
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Target Completion: " . $actdate . "</p>" .
                                                    "<p>Actual Completion: " . $findate . "</p>" .
                                                    "<p>Assets Required: " . $assetdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>" .
                                                    /* "<button type='submit' class='btn btn-primary' name='minprogress'>Mark as In-progress</button>" . */
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    include('../moveBackModal.php');
                ;
        }
    }
}

// pull pending asset requests from requests table
function displayAssetPending(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby, pendate, penby FROM requests WHERE type='asset' AND status='pending'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $pendate = $row['pendate'];
            $penby = $row['penby'];
            $assetdesc = $row['assetdesc'];

            if($_SESSION['user']['user_type'] == 'sadmin'){
                echo 
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $pendate . "</td>".
                "<td>" . $penby . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Moved To Pending On: " . $pendate . "</p>" .
                                                    "<p>Moved To Pending By: " . $penby . "</p>" .
                                                    "<p>Assets Required: " . $assetdesc . "</p>" .
                "<label for='targetDate'>Target Completion Date</label>" .
                "<input type='date' class='form-control' id='targetDateAsset' name='targetDate' placeholder='Enter date' required>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>".
                                    "</tr>";
            }else{
                echo 
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $pendate . "</td>".
                // "<td>" . $penby . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                // "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                // " "    .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Customer Name: " . $custname . "</p>" .
                                                    "<p>Posted On: " . $postdate . "</p>" .
                                                    "<p>Posted By: " . $postby . "</p>" .
                                                    "<p>Expected Completion: " . $compdate . "</p>" .
                                                    "<p>Moved To Pending On: " . $pendate . "</p>" .
                                                    "<p>Moved To Pending By: " . $penby . "</p>" .
                                                    "<p>Assets Required: " . $assetdesc . "</p>" .
                "<label for='targetDate'>Target Completion Date</label>" .
                "<input type='date' class='form-control' id='targetDateAsset' name='targetDate' placeholder='Enter date' required>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>".
                                    "</tr>";
            }

            // echo 
            //     "<tr>" .
            //     "<td>" . $custname . "</td>".
            //     "<td>" . $postdate . "</td>".
            //     "<td>" . $postby . "</td>".
            //     "<td>" . $compdate . "</td>".
            //     "<td>" . $pendate . "</td>".
            //     "<td>" . $penby . "</td>".
            //     "<td>" . $assetdesc . "</td>".
            //     "<td>" . 
            //     "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
            //     " "    .
            //     "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
            //     "</td>" .
            //     "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
            //                             "<div class='modal-dialog' role='document'>" .
            //                                "<form method='post'>" .
            //                                     "<div class='modal-content'>" .
            //                                     "<div class='modal-header'>" .
            //                                         "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
            //                                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
            //                                             "<span aria-hidden='true'>&times;</span>" .
            //                                         "</button>" .
            //                                     "</div>" .
            //                                     "<div class='modal-body'>" .
            //                                         "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
            //                                         "<p>Are you sure you want to reject this request?</p>" .
            //                                     "</div>" .
            //                                     "<div class='modal-footer'>" .
            //                                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
            //                                         "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
            //                                     "</div>" .
            //                                 "</div>" .
            //                                "</form>" .
            //                             "</div>" .
            //                         "</div>" .
            //     "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
            //                             "<div class='modal-dialog' role='document'>" .
            //                                "<form method='post'>" .
            //                                     "<div class='modal-content'>" .
            //                                     "<div class='modal-header'>" .
            //                                         "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
            //                                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
            //                                             "<span aria-hidden='true'>&times;</span>" .
            //                                         "</button>" .
            //                                     "</div>" .
            //                                     "<div class='modal-body'>" .
            //                                         "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
            //                                         "<p>Customer Name: " . $custname . "</p>" .
            //                                         "<p>Posted On: " . $postdate . "</p>" .
            //                                         "<p>Posted By: " . $postby . "</p>" .
            //                                         "<p>Expected Completion: " . $compdate . "</p>" .
            //                                         "<p>Moved To Pending On: " . $pendate . "</p>" .
            //                                         "<p>Moved To Pending By: " . $penby . "</p>" .
            //                                         "<p>Assets Required: " . $assetdesc . "</p>" .
            //     "<label for='targetDate'>Target Completion Date</label>" .
            //     "<input type='date' class='form-control' id='targetDateAsset' name='targetDate' placeholder='Enter date' required>" .
            //                                     "</div>" .
            //                                     "<div class='modal-footer'>" .
            //                                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
            //                                         "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
            //                                     "</div>" .
            //                                 "</div>" .
            //                                "</form>" .
            //                             "</div>" .
            //                         "</div>"
            //     ;
        }
    }
}

// display rejected requests
function displayServiceRejected(){
     global $db;
    $query = "SELECT id, custname, postdate, compdate, servdesc, postby FROM requests WHERE type='service' AND status='rejected'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $servdesc = $row['servdesc'];

            if($_SESSION['user']['user_type'] == 'sadmin'){

                echo 
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                "<a href='#move" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Undo</button></a>" . 
                " " .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Request details</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    include('../moveBackToPoolModal.php');
                                    "</tr>";

            }else{

                echo 
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Request details</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    "</tr>";

            }

            // echo 
            //     "<tr>" .
            //     "<td>" . $custname . "</td>".
            //     "<td>" . $postdate . "</td>".
            //     "<td>" . $postby . "</td>".
            //     "<td>" . $compdate . "</td>".
            //     "<td>" . $servdesc . "</td>".
            //     "<td>" . 
            //     "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
            //     "</td>" .
            //     "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
            //                             "<div class='modal-dialog' role='document'>" .
            //                                "<form method='post'>" .
            //                                     "<div class='modal-content'>" .
            //                                     "<div class='modal-header'>" .
            //                                         "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
            //                                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
            //                                             "<span aria-hidden='true'>&times;</span>" .
            //                                         "</button>" .
            //                                     "</div>" .
            //                                     "<div class='modal-body'>" .
            //                                         "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
            //                                         "<p>Are you sure you want to reject this request?</p>" .
            //                                     "</div>" .
            //                                     "<div class='modal-footer'>" .
            //                                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
            //                                         "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
            //                                     "</div>" .
            //                                 "</div>" .
            //                                "</form>" .
            //                             "</div>" .
            //                         "</div>" .
            //     "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
            //                             "<div class='modal-dialog' role='document'>" .
            //                                "<form method='post'>" .
            //                                     "<div class='modal-content'>" .
            //                                     "<div class='modal-header'>" .
            //                                         "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
            //                                         "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
            //                                             "<span aria-hidden='true'>&times;</span>" .
            //                                         "</button>" .
            //                                     "</div>" .
            //                                     "<div class='modal-body'>" .
            //                                         "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
            //                                         "<p>Request details</p>" .
            //                                     "</div>" .
            //                                     "<div class='modal-footer'>" .
            //                                         "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
            //                                         "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .
            //                                     "</div>" .
            //                                 "</div>" .
            //                                "</form>" .
            //                             "</div>" .
            //                         "</div>"
            //     ;
        }
    }
}

function displayAssetRejected(){
     global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby FROM requests WHERE type='asset' AND status='rejected'";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $postby = $row['postby'];
            $compdate = $row['compdate'];
            $assetdesc = $row['assetdesc'];
            echo 
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                "<a href='#move" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Undo</button></a>" . 
                " " .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Request Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>Request details</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                                    include('../moveBackToPoolModal.php');
                                    "</tr>";
        }
    }
}

function displayAssets(){
     global $db;
    $query = "SELECT id, name, description, stock FROM assets";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $name = $row['name'];
            $description = $row['description'];
            $stock = $row['stock'];
  
            echo 
                "<tr>" .
                //"<td>" . $id . "</td>".
                "<td>" . $name . "</td>".
                "<td>" . $description . "</td>" .
                //"<td>" . $stock . "</td>" .
                /*"<td>" .
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>" .
                "<div class='modal fade' id='reject" . $id . "' tabindex='-1' role='dialog' aria-labelledby='reject" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='rejectModalLabel'>Reject Request</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='reject_req_id' value='" . $id . "'>" .
                                                    "<p>Are you sure you want to reject this request?</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-danger' name='reject'>Reject</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>" .
                "<div class='modal fade' id='edit" . $id . "' tabindex='-1' role='dialog' aria-labelledby='edit" . $id . "Label' aria-hidden='true'>" .
                                        "<div class='modal-dialog' role='document'>" .
                                           "<form method='post'>" .
                                                "<div class='modal-content'>" .
                                                "<div class='modal-header'>" .
                                                    "<h5 class='modal-title id='editModalLabel'>Asset Details</h5>" .
                                                    "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>" .
                                                        "<span aria-hidden='true'>&times;</span>" .
                                                    "</button>" .
                                                "</div>" .
                                                "<div class='modal-body'>" .
                                                    "<input type='hidden' name='edit_req_id' value='" . $id . "'>" .
                                                    "<p>ID: " . $id . "</p>" .
                                                    "<p>Name: " . $name . "</p>" .
                                                    "<p>Description: " . $description . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='save'>Save Changes</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"*/
                "</tr>";
        }
    }
}

function displayCountAssetFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='asset'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountAssetNCRFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='asset' AND branch='NCR'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceNCRFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND branch='NCR'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceITNCRFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='IT' AND branch='NCR'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceITNLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='IT' AND branch='North Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceITSLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='IT' AND branch='South Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceITVFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='IT' AND branch='Visayas'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceITMFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='IT' AND branch='Mindanao'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceD2DNCRFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='day-to-day' AND branch='NCR'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceD2DNLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='day-to-day' AND branch='North Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceD2DSLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='day-to-day' AND branch='South Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceD2DVFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='day-to-day' AND branch='Visayas'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceD2DMFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='day-to-day' AND branch='Mindanao'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceCarNCRFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='car' AND branch='NCR'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceCarNLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='car' AND branch='North Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceCarSLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='car' AND branch='South Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceCarVFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='car' AND branch='Visayas'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceCarMFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND serv_type='car' AND branch='Mindanao'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceNLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND branch='North Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountAssetNLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='asset'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}


function displayCountAssetSLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='asset' AND branch='South Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceSLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND branch='South Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountAssetVFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='asset' AND branch='Visayas'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceVFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND branch='Visayas'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountAssetMFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='asset' AND branch='Mindanao'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountServiceMFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service' AND branch='Mindanao'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }

}

function displayDateRange1(){
    if(isset($_POST['dateRangeButton'])){
        if(isset($_POST['dateRange1'])){
            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));
        }
        echo $dateRange1;
    }
}

function displayDateRange2(){
    if(isset($_POST['dateRangeButton'])){
        if(isset($_POST['dateRange2'])){
            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));
        }
        echo $dateRange2;
    }
}

function displayCountServiceFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND type='service'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountITFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND serv_type='IT'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountD2DFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND serv_type='day-to-day'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountCarFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND serv_type='Car'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountNCRFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND branch='NCR'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountNLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND branch='North Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountSLFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND branch='South Luzon'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountVFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND branch='Visayas'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountMFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND branch='Mindanao'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountAllFromToValue(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2')";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            //echo $total; 

        }
        
        return $total;
        
    }
     
}

function displayCountPoolFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND status='pooled'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountPendingFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND status='pending'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountInProgressFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND status='inprogress'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountCompletedFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND status='completed'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}

function displayCountCompletedFromToValue(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND status='completed'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            //echo $total; 

        }
        
        return $total;
        
    }
     
}

function displayCountRejectedFromTo(){
    global $db;
    
    if(isset($_POST['dateRangeButton'])){
        
        if(isset($_POST['dateRange1'])){

            $dateRange1 = date('Y-m-d', strtotime($_POST['dateRange1']));

        }

        if(isset($_POST['dateRange2'])){

            $dateRange2 = date('Y-m-d', strtotime($_POST['dateRange2']));

        }

        if(!empty($dateRange1) && !empty($dateRange2)){

            $query = "SELECT COUNT(1) FROM requests WHERE (postdate BETWEEN '$dateRange1' AND '$dateRange2') AND status='rejected'";
            $results = mysqli_query($db, $query);
            $row = mysqli_fetch_array($results);

            $total = $row[0];

            echo $total; 

        }
        
    }
     
}


// pull number of pooled requests
function displayCountPooled(){
    global $db;
    $query = "SELECT COUNT(1) FROM requests WHERE status='pooled'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

function displayCountUserPooled(){
    global $db;
    $user = $_SESSION['user']['username'];
    $query = "SELECT COUNT(1) FROM requests WHERE status='pooled' AND postby='$user'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

// pull number of pending requests
function displayCountPending(){
    global $db;
    $query = "SELECT COUNT(1) FROM requests WHERE status='pending'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

function displayCountUserPending(){
    global $db;
    $user = $_SESSION['user']['username'];
    $query = "SELECT COUNT(1) FROM requests WHERE status='pending' AND postby='$user'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

// pull number of in-progress requests
function displayCountInProgress(){
    global $db;
    $query = "SELECT COUNT(1) FROM requests WHERE status='inprogress'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

function displayCountUserInProgress(){
    global $db;
    $user = $_SESSION['user']['username'];
    $query = "SELECT COUNT(1) FROM requests WHERE status='inprogress' AND postby='$user'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

// pull number of completed requests
function displayCountCompleted(){
    global $db;
    $query = "SELECT COUNT(1) FROM requests WHERE status='completed'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

function displayCountUserCompleted(){
    global $db;
    $user = $_SESSION['user']['username'];
    $query = "SELECT COUNT(1) FROM requests WHERE status='completed' AND postby='$user'";
    $results = mysqli_query($db, $query);
    $row = mysqli_fetch_array($results);
    
    $total = $row[0];
    
    echo $total;
}

// display current user
function displayCurrentUser(){
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['fname'];
        
        echo $user;
    }
}
function displayCurrentUserFull(){
    if(isset($_SESSION['user'])){
        $fname = $_SESSION['user']['fname'];
        $lname = $_SESSION['user']['lname'];
        
        echo $fname . " " . $lname;
    }
}

function tRowClose(){
    echo "</tr>";
}

function populateAssetSelect(){
    global $db;
    $query = "SELECT name FROM assets";
    $results = mysqli_query($db, $query);
    
    echo "<option value='' selected disabled>Select an asset</option>";
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_array($results)){
            echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
        }
    }
}

function populateServiceSelect(){
    global $db;
    $query = "SELECT name FROM services";
    $results = mysqli_query($db, $query);
    
    echo "<option value='' selected disabled>Select a service</option>";
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_array($results)){
            echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
        }
    }
}

function populateBranchSelect(){
    global $db;
    $query = "SELECT location FROM branches";
    $results = mysqli_query($db, $query);
    
    echo "<option value='' selected disabled>Select branch</option>";
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_array($results)){
            echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
        }
    }
}

function populateDepartmentSelect(){
    global $db;
    $query = "SELECT name FROM departments";
    $results = mysqli_query($db, $query);
    
    echo "<option value='' selected disabled>Select department</option>";
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_array($results)){
            echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
        }
    }
}