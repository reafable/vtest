<?php include('functions.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Customer Login</title>

    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/formcenter.css">
    <link rel="stylesheet" href="css/loginc.css">
</head>

<body>
    <!-- login test -->
    <!-- <center>
        <h2>login</h2>
        <form action="index.php" method="post">
            <input type="text" name="username">
            <br>
            <input type="password" name="password">
            <br>
            <input type="submit" name="login" value="login">
        </form>
    </center> -->

    <div class="container h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <form class="needs-validation"  method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" autofocus required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                    <div class="invalid-feedback">
                        Enter username.
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                    <small id="passwordNote" class="form-text text-muted">Never share your password with anyone else.</small>
                </div>
                <button type="submit" class="btn btn-primary" name="loginc">Login</button>
            </form>
        </div>
    </div>

    <script src="js/jquery-3.3.1.slim.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>