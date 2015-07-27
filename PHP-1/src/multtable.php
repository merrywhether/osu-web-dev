<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo '<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Multiplication Table</title>
  <link rel="stylesheet" href="multtable.css">
</head>
<body>';

//declare necessary variables
$minMultiplicand;
$maxMultiplicand;
$minMultiplier;
$maxMultiplier;
$multiplicandError = false;
$multiplierError = false;

//basic type and format checking
if (isset($_GET['min-multiplicand'])) {
    $minMultiplicand = $_GET['min-multiplicand'];
    if (is_numeric($minMultiplicand)) {
        if ((int)$minMultiplicand - $minMultiplicand != 0) {
            echo "min-multiplicand must be an integer (no floats).<br>";
            $multiplicandError = true;
        } elseif ($minMultiplicand < 0) {
            echo "min-multiplicand must be a whole number (no negatives).<br>";
            $multiplicandError = true;
        }
    } else {
        echo "min-multiplicand must be an integer (no strings).<br>";
        $multiplicandError = true;
    }
} else {
    echo "Missing parameter min-multiplicand.<br>";
    $multiplicandError = true;
}

if (isset($_GET['max-multiplicand'])) {
    $maxMultiplicand = $_GET['max-multiplicand'];
    if (is_numeric($maxMultiplicand)) {
        if ((int)$maxMultiplicand - $maxMultiplicand != 0) {
            echo "max-multiplicand must be an integer (no floats).<br>";
            $multiplicandError = true;
        } elseif ($maxMultiplicand < 0) {
            echo "max-multiplicand must be a whole number (no negatives).<br>";
            $multiplicandError = true;
        }
    } else {
        echo "max-multiplicand must be an integer (no strings).<br>";
        $multiplicandError = true;
    }
} else {
    echo "Missing parameter max-multiplicand.<br>";
    $multiplicandError = true;
}

if (isset($_GET['min-multiplier'])) {
    $minMultiplier = $_GET['min-multiplier'];
    if (is_numeric($minMultiplier)) {
        if ((int)$minMultiplier - $minMultiplier != 0) {
            echo "min-multiplier must be an integer (no floats).<br>";
            $multiplierError = true;
        } elseif ($minMultiplier < 0) {
            echo "min-multiplier must be a whole number (no negatives).<br>";
            $multiplierError = true;
        }
    } else {
        echo "min-multiplier must be an integer (no strings).<br>";
        $multiplierError = true;
    }
} else {
    echo "Missing parameter min-multiplier.<br>";
    $multiplierError = true;
}

if (isset($_GET['max-multiplier'])) {
    $maxMultiplier = $_GET['max-multiplier'];
    if (is_numeric($maxMultiplier)) {
        if ((int)$maxMultiplier - $maxMultiplier != 0) {
            echo "max-multiplier must be an integer (no floats).<br>";
            $multiplierError = true;
        } elseif ($maxMultiplier < 0) {
            echo "max-multiplier must be a whole number (no negatives).<br>";
            $multiplierError = true;
        }
    } else {
        echo "max-multiplier must be an integer (no strings).<br>";
        $multiplierError = true;
    }
} else {
    echo "Missing parameter max-multiplier.<br>";
    $multiplierError = true;
}

//range checking
if (!$multiplicandError && $minMultiplicand > $maxMultiplicand) {
    echo "Minimum multiplicand larger than maximum<br>";
    $multiplicandError = true;
}
if (!$multiplierError && $minMultiplier > $maxMultiplier) {
    echo "Minimum multiplier larger than maximum<br>";
    $multiplierError = true;
}

if (!$multiplicandError && !$multiplierError) {
    echo "<div><h1>Multiplication table</h1>";
    echo '<table><thead><tr><th class="empty"></th>'; // first cell empty
    for ($i = $minMultiplier; $i <= $maxMultiplier; $i++) {
        echo "<th>$i</th>";
    }
    echo "</tr></thead><tbody>";
    for ($i = $minMultiplicand; $i <= $maxMultiplicand; $i++) {
        echo "<tr><th>$i</th>";
        for ($j = $minMultiplier; $j <= $maxMultiplier; $j++) {
            echo "<td>" . ($i * $j) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table></div>";
}
echo "<hr>";
?>
<h2>Generate a new multiplication table</h2>
<form method="GET">
  <div>
    <label for="min-multiplicand">Minimum Multiplicand</label>
    <input type="number" id="min-multiplicand" name="min-multiplicand" placeholder="Enter a whole number">
  </div>
  <div>
    <label for="min-multiplicand">Maximum Multiplicand</label>
    <input type="number" id="max-multiplicand" name="max-multiplicand" placeholder="Enter a whole number">
  </div>
  <div>
    <label for="min-multiplicand">Minimum Multiplier</label>
    <input type="number" id="min-multiplier" name="min-multiplier" placeholder="Enter a whole number">
  </div>
  <div>
    <label for="min-multiplicand">Maximum Multiplier</label>
    <input type="number" id="max-multiplier" name="max-multiplier" placeholder="Enter a whole number">
  </div>
  <div>
    <input type="submit" value="Submit">
  </div>
</form>
</body>
</html>
