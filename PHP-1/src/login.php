<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (isset($_REQUEST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("Location: login.php", true);
    die();
} elseif (isset($_SESSION['username'])) {
    header("Location: content1.php", true);
    die();
} else {
    echo '<!DOCTYPE html>
          <html>
          <head>
            <meta charset="UTF-8">
            <title>Login Page</title>
          </head>
          <body>
            <form method="POST" action="content1.php">
              <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" placeholder="Enter your username">
              </div>
              <input type="submit" value="Login">
            </form>
          </body>
          </html>';
}
?>
