<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
include "secrets.php";  //constants defined in non-public file

$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
//bail completely if any connection errors
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
    $errors = array();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //process new video addition
        if (isset($_POST["new-video"])) {
            $validationError = false;
            //validate video-name
            if (!isset($_POST["video-name"]) || strlen($_POST["video-name"]) == 0) {
                array_push($errors, "Could not add video: name is required");
                $validationError = true;
            }
            //validate video-length
            if (strlen($_POST["video-length"]) > 0 && !ctype_digit($_POST["video-length"]) || $_POST["video-length"] == "0") {
                array_push($errors, "Could not add video: length must be a natural number (aka a positive integer)");
                $validationError = true;
            }
            //if no validation errors, update DB
            if (!$validationError) {
                $sqlError = false;
                $name = $_POST["video-name"];
                $category = $_POST["video-category"];
                if (!strlen($category)) {
                    $category = null;
                }
                $length = $_POST["video-length"];
                if (!strlen($length)) {
                    $length = null;
                }

                //attempt to insert new record
                if (!($stmt = $mysqli->prepare("INSERT INTO videos(name, category, length) VALUES (?, ?, ?)"))) {
                    array_push($errors, "Could not add video: PHP/MySQL error");
                    $sqlError = true;
                }
                if (!$sqlError && !$stmt->bind_param("ssi", $name, $category, $length)) {
                    array_push($errors, "Could not add video: PHP/MySQL error");
                    $sqlError = true;
                }
                if (!$sqlError && !$stmt->execute()) {
                    if ($stmt->errno == 1062) {
                        array_push($errors, "Could not add video: $name already in database");
                    } else {
                        array_push($errors, "Could not add video: PHP/MySQL error ($stmt->errno)");
                    }
                    $sqlError = true;
                }
                $stmt->close();

                //redirect to GET on success
                if (!$sqlError) {
                    header("location: index.php", true);
                    die();
                }
            }
        //process video filtering (could be a GET, but everything else is a POST)
        } elseif (isset($_POST["filter-videos"])) {
            if ($_POST["filter"] == "all") {
                header("location: index.php", true);
                die();
            } else {
                //set GET parameter and redirect to GET
                header("location: index.php?filter=$_POST[filter]", true);
                die();
            }
        //process deleting all
        } elseif (isset($_POST["delete-all"])) {
            $mysqli->query("DELETE FROM videos");
            header("location: index.php", true);
            die();
        //process other requests
        } else {
            foreach ($_POST as $key => $val) {
                //look for known action pattern
                preg_match("/(\w+)-video-(\d+)/", $key, $matches);
                if (count($matches) == 3) {
                    //switch on action, act on id
                    if ($matches[1] == "rent") {
                        $mysqli->query("UPDATE videos SET rented=TRUE WHERE id='$matches[2]'");
                    } elseif ($matches[1] == "return") {
                        $mysqli->query("UPDATE videos SET rented=FALSE WHERE id='$matches[2]'");
                    } elseif ($matches[1] == "delete") {
                        $mysqli->query("DELETE FROM videos WHERE id='$matches[2]'");
                    }
                    header("location: index.php", true);
                    die();
                }
            }
        }
    }

    //get active filter
    $filter = "all";
    if (isset($_GET["filter"])) {
        $filter = $_GET["filter"];
    }
    //get videos ordered alphabetically
    $res = $mysqli->query("SELECT * FROM videos ORDER BY name ASC");
    $res->fetch_all();

    echo "<!DOCTYPE html>
          <html>
          <head>
            <meta charset='UTF-8'>
            <title>Video Store</title>
            <link rel='stylesheet' href='style.css'>
          </head>
          <body>";

    //display any errors
    if (count($errors)) {
        echo "<div class='error-list'>
              <h4>Errors:</h4>
              <ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>
              </div>";
    }

    echo "<form method='POST'>
          <div class='videos-container'>
          <table>
          <caption><h1>Video Catalog</h1></caption>
          <thead>
            <tr>
              <th class='name'>Name</th>
              <th class='category'>Category</th>
              <th class='length'>Length</th>
              <th class='availability'>Availability?</th>
              <th class='delete'>Delete</th>
            </tr>
          </thead>
          <tbody>";

    //sort through videos, building categories list and drawing table
    $categories = array();
    $total = 0;
    foreach ($res as $video) {
        //build and count categories
        $total++;
        if ($video["category"] == "") {
            ;  //noop
        } elseif (isset($categories[$video["category"]])) {
            $categories[$video["category"]]++;
        } else {
            $categories[$video["category"]] = 1;
        }

        //draw row if video matches current filter
        if ($filter == "all" || $video["category"] == $filter
        || ($filter == "No category" && $video["category"] == "")) {
            echo "<tr>";
            foreach ($video as $key => $attr) {
                if ($key == "rented") {
                    if ($attr == 0) {
                        echo "<td class='availability'>Available <input type='submit'
                              value='Check out' name='rent-video-$video[id]'></td>";
                    } else {
                        echo "<td class='availability'>Checked out <input type='submit'
                              value='Check in' name='return-video-$video[id]'></td>";
                    }
                } elseif ($key != "id") {
                    echo "<td>" . htmlspecialchars($attr) . "</td>";
                }
            }
            echo "<td class='delete'>
                  <input type='submit' value='Delete' name='delete-video-$video[id]'>
                  </td>
                  </tr>";
        }
    }
    //sort the categories alphabetically
    ksort($categories);

    echo "</tbody>
          </table>
          <label for='filter'>Category filter:</label>
          <select id='filter' name='filter'>";

    //draw filter menu with proper filter set selected
    if ($filter == "all") {
        echo "<option value='all' selected>All ($total)</option>";
    } else {
        echo "<option value='all'>All ($total)</option>";
    }
    foreach ($categories as $category => $count) {
        if ($filter == $category) {
            echo "<option value='$category' selected>$category ($count)</option>";
        } else {
            echo "<option value='$category'>$category ($count)</option>";
        }
    }

    echo "</select>
          <input type='submit' value='Filter' name='filter-videos'>
          <input type='submit' value='Delete all videos' name='delete-all'>
          </div>
          </form>";

    echo "<hr>
          <h2>Add a new video</h2>
          <form class='new-video' method='POST'>
            <label for='video-name'>Name:</label>
            <input type='text' id='video-name' name='video-name' placeholder='Enter a name' autofocus>
            <label for='video-category'>Category:</label>
            <input type='text' id='video-category' name='video-category' placeholder='Enter a category'>
            <label for='video-length'>Length:</label>
            <input type='text' id='video-length' name='video-length' placeholder='Enter a length'>
            <input type='submit' value='Add Video' name='new-video'>
          </form>";

    echo "</body>
          </html>";
}
?>
