# iotSimulator-php

The `iotSimulator.php` file contains a function for creating JSON Web APIs to simulate IoT sensor readings.  Provide a template containing the desired JSON data structure along with the upper and lower random number constraint limits for each sensor reading.  As the PHP page is refreshed, new random readings will appear each minute. Readings from previous minutes will retain their values during page refreshes rather than getting replaced with new random data.

## Installation

Copy `iotSimulator.php` to a web directory that supports rendering PHP scripts on your hosting provider or local system.

Create a file (for example, `tanks.php`) in the same web directory and include the following content:

```raw
<?php

require('iotSimulator.php');

$template = <<<JSON
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "%timestamp",
      "value": %val(70,71)
    }
  }
JSON;

$useGMT = true;

// Sample format: 2019-04-19 20:39 GMT
$timeFormat = 'Y-m-d H:i T';
render_json($template, $useGMT, $timeFormat);
```

Modify the content in the lines following the `$template =` line and the `JSON;` line with the JSON object or objects you desire to render.

Save the `tanks.php` file and navigate to the URL in your browser.  For example: <https://thisdavej.com/api/tanks.php>.

The rendered results will look something like this since by default 3 rows (minutes) of data are displayed:

```json
[
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "2019-04-19 19:03 GMT",
      "value": 70.62
    }
  },
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "2019-04-19 19:02 GMT",
      "value": 70.55
    }
  },
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "2019-04-19 19:01 GMT",
      "value": 70.7
    }
  }
]
```

Note that the php function added a "[" at start and a "]" at the end to produce an array of JSON objects with each JSON object rendered based on the template provided.

## Documentation

### Template

The template can contain one or more JSON objects to render as a "row" of data for a given minute.  We'll use the following template for our example:

```raw
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
```

The dynamic template fields of interest are:

- `%timestamp` - This is substituted with the current timestamp when rendering the page.
- %val(x,y,[decimals]) - This will render a random number between x and y.  If decimals is not supplied, the resulting number will be rounded to 2 decimals.

In our example, we render a random sensor reading between 70 and 71 (with a default of 2 decimal places) for `Tank1` and a reading between 72 and 73 (with 1 decimal place) for `Tank2`.

The rendered results will look something like this since by default 3 rows (minutes) of data are displayed:

```json
[
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "2019-04-19 19:30 GMT",
      "value": 70.28
    }
  },
  {
    "Tank": "Tank2",
    "watertemp": {
      "time": "2019-04-19 19:30 GMT",
      "value": 72.9
    }
  },
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "2019-04-19 19:29 GMT",
      "value": 70.32
    }
  },
  {
    "Tank": "Tank2",
    "watertemp": {
      "time": "2019-04-19 19:29 GMT",
      "value": 72.5
    }
  },
  {
    "Tank": "Tank1",
    "watertemp": {
      "time": "2019-04-19 19:28 GMT",
      "value": 70.41
    }
  },
  {
    "Tank": "Tank2",
    "watertemp": {
      "time": "2019-04-19 19:28 GMT",
      "value": 72.4
    }
  }
]
```

### render_json function

Usage

render_json($template, $useGMT, $timeFormat);

Parameters

- `$template` - JSON template to render
- `$useGMT` - Set to `false` to render times in the local time zone of the server rather than GMT. (default: `true`)
- `$timeFormat` - the PHP time format to use for rendering.  See the PHP [date](https://www.php.net/manual/en/datetime.formats.date.php) and [time](https://www.php.net/manual/en/datetime.formats.time.php) formats for more information. (default: `'Y-m-d H:i T')

### URL parameters

The PHP file you create (e.g. `tanks.php`) can be supplied with one optional parameter:

- `rows` - specifies the number of rows of one-minute data render.  For example, use <https://thisdavej.com/api/tanks.php?rows=5> to render 5 rows of data.

## License

MIT © [Dave Johnson (thisDaveJ)](https://thisdavej.com)
