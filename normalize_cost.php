<?php
//take an array of floats or strings
//filter out possible $ signs
///count the number of positive values and negative values
//if the count of negative values is above 80%, flip the sign
//if the sign is flipped, discard the newly negative and previously positive values
//else, discard all the negative values


$test_array = [1.46,-2.22,3.33,5.55,7.88];
$test_array2 = [-1.46,-2.22,-3.33,5.55,-7.88, -7.33,1.22,1.22,-1.0,2,0,-2.0,-3.0,-9.4];

function normalize_costs($arr) {
    $positives = [];
    $negatives = [];

    foreach ($arr as $x) {
        if ($x < 0) {
            $negatives[] = $x;
        } else {
            $positives[] = $x; 
        }
        
    }
    $normalized_array = [];
    echo "percentage of negatives: ";
    echo count($negatives) / count($arr);
    if (count($negatives) / count($arr) > .5) {
        foreach ($arr as $x) {
            if ($x < 0) {
                $normalized_array[] = $x*-1;
            } 
        }
    } else {
        
        $normalized_array = $positives;
    }

    return $normalized_array;

}

$array = normalize_costs($test_array);

foreach ($array as $key => $val) {
    echo "\n $val \n";
 }
?>

