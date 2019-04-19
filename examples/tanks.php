<?php

require('iotSimulator.php');

$template = <<<JSON
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "%timestamp",
      "value": %val(70,71)
    }
  },
  {
    "Tank": "Tank2",
    "watertemp": {
      "time": "%timestamp",
      "value": %val(72,73,1)
    }
  }
JSON;

$useGMT = true;

// Sample format: 2019-04-19 20:39 GMT
$timeFormat = 'Y-m-d H:i T';
render_json($template, $useGMT, $timeFormat);