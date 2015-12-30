<?php

/**
 * Full Name: Iszak Bryan
 * ITS Username: ibryan02
 * Module Name: Web Programming using PHP
 * Tutor Name: Tobi Brodie
 */

require_once 'functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">

        <title>Log Statistics</title>
    </head>
    <body>

        <h1>Log Statistics</h1>

<?php
$logs = findLogs('/home/itapps/lo/p1/tma/');

if ($logs) {
    foreach($logs as $log) {
        $statistics = parseLogFile($log);

        // Error parsing file
        if (!$statistics) {
            $log = htmlentities($log);
            echo <<<HTML
                <p>File {$log} not readable</p>
HTML;
            continue;
        }

        $month = htmlentities($statistics['month']);

        echo <<<HTML
        <h2>{$month}</h2>
        <dl>
            <dt>Total requests</dt>
            <dd>{$statistics['totalRequests']}</dd>

            <dt>Total requests for articles</dt>
            <dd>{$statistics['articleRequests']}</dd>

            <dt>Total bandwidth</dt>
            <dd>{$statistics['totalBandwidth']} bytes</dd>

            <dt>Total files not found (404)</dt>
            <dd>{$statistics['totalNotFound']}</dd>\n
HTML;

        if (count($statistics['filesNotFound']) > 0) {
            $filesNotFound = $statistics['filesNotFound'];

            echo <<<HTML
            <dt>Files not Found</dt>
            <dd>
                <ul>\n
HTML;
            foreach ($filesNotFound as $file) {
                $file = htmlentities($file);
                echo <<<HTML
                    <li>{$file}</li>\n
HTML;
            }

            echo <<<HTML
                </ul>
            </dd>
        </dl>\n\n
HTML;
        }
    }
} else {
    echo <<<HTML
        <p>Error reading directory</p>
HTML;
}
?>

    </body>
</html>
