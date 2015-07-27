<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php", true);
    die();
}

echo '<!DOCTYPE html>
      <html>
      <head>
        <meta charset="UTF-8">
          <title>Content 2</title>
      </head>
      <body>';
echo '<button><a href="content1.php">Check out Content 1</a></button>';
echo "</body></html>";
?>
