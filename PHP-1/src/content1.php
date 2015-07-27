<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$loginError = false;
session_start();
//check method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //if already logged in, POST not allowed, redirecting to GET version
    if (isset($_SESSION['username']) && isset($_POST['username']) && $_SESSION['username'] == $_POST['username']) {
        header("location: content1.php", true);
        die();
    } else {
        //ensure username submitted
        if (isset($_POST['username']) && strlen($_POST['username']) > 0) {
            $_SESSION['username'] = $_POST['username'];
            $_SESSION['visitCount'] = -1;
            header("location: content1.php", true);
            die();
        } else {
            $loginError = true;
        }
    }
} else {
    //if not posting and not authed, redirect to login
    if (!isset($_SESSION['username'])) {
        header("location: login.php", true);
        die();
    } else {
        $_SESSION['visitCount']++;
    }
}

echo '<!DOCTYPE html>
      <html>
      <head>
        <meta charset="UTF-8">
          <title>Content 1</title>
      </head>
      <body>';

if ($loginError) {
    echo '<p>A username must be entered. ';
    echo 'Click <a href="login.php">here</a> to return to the login screen.</p>';
} else {
    echo "<p>Hello $_SESSION[username], you've visited this page $_SESSION[visitCount] times before. ";
    echo 'Click <a href="login.php?logout=true">here</a> to logout.</p>';
    echo '<button><a href="content2.php">Check out Content 2</a></button>';
}
echo "</body></html>";
?>
