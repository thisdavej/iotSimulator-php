<?php

// Un-comment the next two lines to display error details if the page is not rendering correctly.
// ini_set('error_reporting', E_ALL);
// ini_set('display_errors', 'On');  //On or Off

function getUrlParam($key, $defaultValue)
{
    $val = filter_input(
        INPUT_GET,
        $key,
        FILTER_SANITIZE_NUMBER_INT
    );
    return $val ? $val : $defaultValue;
}

function clamp($current, $min, $max)
{
    return max($min, min($max, $current));
}


function make_seed($time)
{
    // Convert the current time to an integer.  13:03 -> 1303
    $t = explode(":", date('G:i', $time));
    return (int)($t[0] . $t[1]);
}


function rand_float($min, $max, $decimals)
{
    if ($min > $max) {
        $temp = $min;
        $min = $max;
        $max = $temp;
    }
    $r = (float)rand() / (float)getrandmax();
    $float = ($min + $r * abs($max - $min));
    return number_format($float, $decimals);
}


function render_json($template, $useGMT = true, $timeFormat = 'Y-m-d H:i T')
{
    $rows = (int)getUrlParam("rows", 3);
    $rows = clamp($rows, 1, 50);

    // remove trailing comma from the template if it is included
    $template = preg_replace("|,\s*$|", "", $template);

    $dateFormatFn = $useGMT ? 'gmdate' : 'date';

    header('Content-Type: application/json');
    echo "[\n";

    foreach (range(1, $rows) as $index) {
        $minutes = 1 - $index * 1;
        $time = strtotime("{$minutes} minutes");

        $timestamp = $dateFormatFn($timeFormat, $time);

        // Create seed for every minute interval based on the time (e.g. 13:03 -> 1303)
        // so can reproduce the same numbers in history if page invoked a minute
        // later, for example.
        $seed = make_seed($time);
        srand($seed);

        $d = $template;

        $d = str_replace("%timestamp", $timestamp, $d);

        // Replace %val(x,y,decimals) with a random float value between x and y rounded to decimals.
        // %val(x,y) is also valid and since no decimals are supplied, a default value of 2 will be used.
        $d = preg_replace_callback(
            '|%val\(\s*(\d+)\s*,\s*(\d+)\s*(,\s*(\d)\s*)?\)|',
            function ($m) {
                $decimals = isset($m[4]) ? $m[4] : 1;
                return rand_float($m[1], $m[2], $decimals);
            },
            $d
        );

        $comma = ($index == $rows) ? "" : ",\n";
        $d = $d . $comma;

        echo $d;
    }
    echo  "\n]";
}


// Call this file (iotSimulator.php) from another page. Example:

// Filename: tanks.php

// <?php

// require('iotSimulator.php');

// $template = <<<JSON
//   {
//     "Tank": "Tank1",
//     "watertemp": {
//       "time": "%timestamp",
//       "value": %val(70,71)
//     }
//   },
//   {
//     "Tank": "Tank2",
//     "watertemp": {
//       "time": "%timestamp",
//       "value": %val(72,73,1)
//     }
//   }
// JSON;

// $useGMT = true;

// // Sample format: 2019-04-19 20:39 GMT
// $timeFormat = 'Y-m-d H:i T';
// render_json($template, $useGMT, $timeFormat);
