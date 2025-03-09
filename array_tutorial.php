<?php

$array2D = [
    [1, 2, 3],
    [4, 5, 6],
    [7, 8, 9]
];

$replacement = [980, 981, 982];

$array2D[1] = $replacement;

foreach ($array2D as $key => $val) {
    echo "Row $key: " . implode(", ", $val) . "\n";
}
?>
