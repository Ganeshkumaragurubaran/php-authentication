<?php
# Include connection
require_once "./config.php";

# Define variables and initialize with empty values
$username_err = $email_err = $mobile_number_err = $password_err = $confirm_password_err = "";
$username = $email = $mobile_number = $password = $confirm_password = "";

# Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  # Validate username
  if (empty(trim($_POST["username"]))) {
    $username_err = "Please enter a username.";
  } else {
    $username = trim($_POST["username"]);
    if (!ctype_alnum(str_replace(array("@", "-", "_"), "", $username))) {
      $username_err = "Username can only contain letters, numbers and symbols like '@', '_', or '-'.";
    } else {
      # Prepare a select statement
      $sql = "SELECT id FROM users WHERE username = ?";

      if ($stmt = mysqli_prepare($link, $sql)) {
        # Bind variables to the statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_username);

        # Set parameters
        $param_username = $username;

        # Execute the prepared statement 
        if (mysqli_stmt_execute($stmt)) {
          # Store result
          mysqli_stmt_store_result($stmt);

          # Check if username is already registered
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $username_err = "This username is already registered.";
          }
        } else {
          echo "<script>" . "alert('Oops! Something went wrong. Please try again later.')" . "</script>";
        }

        # Close statement 
        mysqli_stmt_close($stmt);
      }
    }
  }

  # Validate email 
  if (empty(trim($_POST["email"]))) {
    $email_err = "Please enter an email address";
  } else {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $email_err = "Please enter a valid email address.";
    } else {
      # Prepare a select statement
      $sql = "SELECT id FROM users WHERE email = ?";

      if ($stmt = mysqli_prepare($link, $sql)) {
        # Bind variables to the statement as parameters
        mysqli_stmt_bind_param($stmt, "s", $param_email);

        # Set parameters
        $param_email = $email;

        # Execute the prepared statement 
        if (mysqli_stmt_execute($stmt)) {
          # Store result
          mysqli_stmt_store_result($stmt);

          # Check if email is already registered
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email is already registered.";
          }
        } else {
          echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
        }

        # Close statement
        mysqli_stmt_close($stmt);
      }
    }
  }
  # Validate mobile number
  if (empty(trim($_POST["mobile_number"]))) {
  $mobile_number_err = "Please enter a mobile number.";
  } else {
  $mobile_number = trim($_POST["mobile_number"]);
  if (!preg_match("/^[0-9]{10}$/", $mobile_number)) {
      $mobile_number_err = "Please enter a valid 10-digit mobile number.";
  }
  }
  # Validate password
   if (empty(trim($_POST["password"]))) {
     $password_err = "Please enter a password.";
   } else {
     $password = trim($_POST["password"]);
     if (strlen($password) < 8) {
         $password_err = "Password must contain at least 8 or more characters.";
     }
   
     # Validate confirm password
     if (empty(trim($_POST["confirm_password"]))) {
         $confirm_password_err = "Please confirm the password.";
     } else {
         $confirm_password = trim($_POST["confirm_password"]);
         if ($confirm_password !== $password) {
             $confirm_password_err = "The password and confirm password do not match.";
         }
     }
   }

  # Check input errors before inserting data into database
  if (empty($username_err) && empty($email_err) && empty($mobile_number_err) && empty($password_err) && empty($confirm_password_err)) {
    # Prepare an insert statement
    $sql = "INSERT INTO users(username, email, mobile, password) VALUES (?, ?, ?,?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
      # Bind varibales to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "ssss", $param_username, $param_email, $param_mobile, $param_password);

      # Set parameters
      $param_username = $username;
      $param_email = $email;
      $param_mobile = $mobile_number;
      $param_password = password_hash($password, PASSWORD_DEFAULT);

      # Execute the prepared statement
      if (mysqli_stmt_execute($stmt)) {
        echo "<script>" . "alert('Registeration completed successfully. Login to continue.');" . "</script>";
        echo "<script>" . "window.location.href='./login.php';" . "</script>";
        exit;
      } else {
        echo "<script>" . "alert('Oops! Something went wrong. Please try again later.');" . "</script>";
      }

      # Close statement
      mysqli_stmt_close($stmt);
    }
  }

  # Close connection
  mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User login system</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
  <link rel="stylesheet" href="./css/main.css">
  <link rel="shortcut icon" href="./img/favicon-16x16.png" type="image/x-icon">
</head>

<body>            
  <div class="container">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-lg-5">
        <div class="form-wrap border rounded p-4">
          <h1>Sign up</h1>
          <p>Please fill this form to register</p>
          <!-- form starts here -->
          <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" novalidate>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" name="username" id="username" value="<?= $username; ?>">
              <small class="text-danger"><?= $username_err; ?></small>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" id="email" value="<?= $email; ?>">
              <small class="text-danger"><?= $email_err; ?></small>
            </div>
            <div class="mb-3">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="text" class="form-control" name="mobile_number" id="mobile_number" value="<?= $mobile_number; ?>">
                <small class="text-danger"><?= $mobile_number_err; ?></small>
            </div>
            <div class="mb-2">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" id="password" value="<?= $password; ?>">
              <small class="text-danger"><?= $password_err; ?></small>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" value="<?= $confirm_password; ?>">
                <small class="text-danger"><?= $confirm_password_err; ?></small>
            </div>
            <div class="mb-3">
              <input type="submit" class="btn btn-primary form-control" name="submit" value="Sign Up">
            </div>
            <p class="mb-0">Already have an account ? <a href="./login.php">Log In instead</a></p>
          </form>
          <!-- form ends here -->
        </div>
      </div>
    </div>
  </div>
</body>

</html>