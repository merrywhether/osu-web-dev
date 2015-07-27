<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-type: application/json');
$parameters = null;
if (count($_POST)) {
    $parameters = $_POST;
} elseif (count($_GET)) {
    $parameters = $_GET;
}

$arr = array("Type" => $_SERVER["REQUEST_METHOD"],
             "parameters" => $parameters);

echo json_encode($arr);
?>
