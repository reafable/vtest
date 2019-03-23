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

function moveRequestToInProgress(){
    global $db;
    
    $edit_req_id = $_POST['edit_req_id'];
    
    $query = "UPDATE requests
              SET
              status='inprogress'
              WHERE
              id='$edit_req_id'";
    mysqli_query($db, $query);
}

function moveRequestToCompleted(){
    global $db;
    
    $edit_req_id = $_POST['edit_req_id'];
    
    $query = "UPDATE requests
              SET
              status='completed'
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
    
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
    }
    
    $custname = e($_POST['custname']);
    $postdate = $date;
    $compdate = e($_POST['compdate']);
    $servdesc = e($_POST['servdesc']);
    
    $query = "INSERT INTO requests 
              (id, type, custname, postdate, compdate, servdesc, postby, status) 
              VALUES 
              (NULL, 'service', '$custname', '$postdate', '$compdate', '$servdesc', '$user', 'pooled')";
    
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
    
    if(isset($_SESSION['user'])){
        $user = $_SESSION['user']['username'];
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
              (id, type, custname, postdate, compdate, assetdesc, postby, status) 
              VALUES 
              (NULL, 'asset', '$custname', '$postdate', '$compdate', '" . implode(', ', $insertVal) . "', '$user', 'pooled')";
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
    $password1 = e($_POST['password1']);
    $password2= e($_POST['password2']);
    $user_type = e($_POST['user_type']);
    //$status = e($_POST['status']);
    $fname = e($_POST['fname']);
    $lname = e($_POST['lname']);
    
    
    if($password1 == $password2){
        $query = "INSERT INTO users (id, username, password, user_type, status, fname, lname) VALUES ('NULL', '$username', '$password1', '$user_type', 'active', '$fname', '$lname')";
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
	$password = e($_POST['password']);

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
			if ($logged_in_user['user_type'] == 'admin') {

				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "yay";
				header('location: admin/home.php');		  
			}else{
				$_SESSION['user'] = $logged_in_user;
				$_SESSION['success']  = "yay";

				header('location: requests/type.php');
			}
		}else {
//			array_push($errors, "Wrong username or password.");
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
  Your username or password may be wrong. Or your account is inactive.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
		}
	}else{
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
  Your username or password may be wrong. Or your account is inactive.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>';
    }
}

function isAdmin(){
	if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] == 'admin' ) {
		return true;
	}else{
		return false;
	}
}

function displayRequests(){
    global $db;
    $query = "SELECT id, type, custname, postdate, compdate, postby, status from requests";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            
            $id = $row['id'];
            $type = $row['type'];
            $custname = $row['custname'];
            $postdate = $row['postdate'];
            $compdate = $row['compdate'];
            $postby = $row['postby'];
            $status = $row['status'];
            
            echo
                "<tr>" .
                "<td>" . $id . "</td>" .
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

//pull data from users table
function displayUsers(){
    global $db;
    $query = "SELECT id, status, username, fname, lname, password FROM users";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            
            $id = $row['id'];
            
            echo
                "<tr>" .
                "<td>" . $row['id'] . "</td>" . 
                "<td>" . $row['status'] . "</td>" .
                "<td>" . $row['username'] . "</td>" .
                "<td>" . $row['fname'] . "</td>" .
                "<td>" . $row['lname'] . "</td>" .
                "<td>" . $row['password'] . "</td>" .
                "<td>" . 
                "<a href='#edit" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-primary btn-sm'>View</button></a>" . 
                "</td>".
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
                                                    "<p>ID: </p>" .
                                                    "<p>Name: </p>" .
                                                    "<p>Description: </p>" .
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
    }
}

// pull pooled service requests from requests table
function displayServicePooled(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, servdesc, postby FROM requests WHERE type='service' AND status='pooled'";
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
            $servdesc = $row['servdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
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
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
        }
    }
}

function displayAssetPooled(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby FROM requests WHERE type='asset' AND status='pooled'";
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
            $assetdesc = $row['assetdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
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
                                                    "<p>Assets Required: " . $assetdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='mpending'>Move to Pending</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
        }
    }
}

function displayServicePending(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, servdesc, postby FROM requests WHERE type='service' AND status='pending'";
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
            $servdesc = $row['servdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
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
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                    //insert form here: target completion date
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
        }
    }
}

function displayServiceInprogress(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, servdesc, postby FROM requests WHERE type='service' AND status='inprogress'";
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
            $servdesc = $row['servdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . "Placeholder" . "</td>".
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
                                                    "<p>Service Description: " . $servdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
        }
    }
}

function displayAssetInprogress(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby FROM requests WHERE type='asset' AND status='inprogress'";
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
            $assetdesc = $row['assetdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
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
                                                    "<p>Service Description: " . $assetdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='mcompleted'>Mark as Completed</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
        }
    }
}

function displayServiceCompleted(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, servdesc, postby FROM requests WHERE type='service' AND status='completed'";
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
            $servdesc = $row['servdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $servdesc . "</td>".
                "<td>" . 
                /* "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    . */
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

function displayAssetCompleted(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby FROM requests WHERE type='asset' AND status='completed'";
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
            $assetdesc = $row['assetdesc'];
            echo
                "<tr>" .
                "<td>" . $custname . "</td>".
                "<td>" . $postdate . "</td>".
                "<td>" . $postby . "</td>".
                "<td>" . $compdate . "</td>".
                "<td>" . $assetdesc . "</td>".
                "<td>" . 
                /* "<a href='#reject" . $id . "' data-toggle='modal'>" . "<button type='button' class='btn btn-danger btn-sm'>Reject</button></a>" . 
                " "    . */
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
                                                    "<p>Assets Required: " . $assetdesc . "</p>" .
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

// pull pending asset requests from requests table
function displayAssetPending(){
    global $db;
    $query = "SELECT id, custname, postdate, compdate, assetdesc, postby FROM requests WHERE type='asset' AND status='pending'";
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
                                                    "<p>Assets Required: " . $assetdesc . "</p>" .
                                                "</div>" .
                                                "<div class='modal-footer'>" .
                                                    "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Cancel</button>" .
                                                    "<button type='submit' class='btn btn-primary' name='minprogress'>Move to Ongoing</button>" .
                                                "</div>" .
                                            "</div>" .
                                           "</form>" .
                                        "</div>" .
                                    "</div>"
                ;
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
                                    "</div>"
                ;
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
                                    "</div>"
                ;
        }
    }
}

function displayAssets(){
     global $db;
    $query = "SELECT id, name, description FROM assets";
    $results = mysqli_query($db, $query);
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_assoc($results)){
            $id = $row['id'];
            $name = $row['name'];
            $description = $row['description'];
  
            echo 
                "<tr>" .
                "<td>" . $id . "</td>".
                "<td>" . $name . "</td>".
                "<td>" . $description . "</td>".
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
                                    "</div>";
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

// pull number of pending requests
function displayCountPending(){
    global $db;
    $query = "SELECT COUNT(1) FROM requests WHERE status='pending'";
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

// pull number of completed requests
function displayCountCompleted(){
    global $db;
    $query = "SELECT COUNT(1) FROM requests WHERE status='completed'";
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
    
    if($results-> num_rows > 0){
        while($row = mysqli_fetch_array($results)){
            echo "<option value='" . $row[0] . "'>" . $row[0] . "</option>";
        }
    }
}