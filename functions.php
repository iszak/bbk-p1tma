<?php

/**
 * Full Name: Iszak Bryan
 * ITS Username: ibryan02
 * Module Name: Web Programming using PHP
 * Tutor Name: Tobi Brodie
 */

/**
 * Determines if file is a log file
 *
 * @return boolean
 */
function isLogFile($path) {
    return pathinfo($path, PATHINFO_EXTENSION) == 'log';
}

/**
 * Read the redirectory looking for .log files and return found
 * log files
 *
 * @param string $path The path to the log directory with the trailing slash
 * @return string[]|false
 */
function findLogs($path) {
    if (!is_dir($path) && is_readable($path)) {
        return false;
    }

    $logs = array();
    if ($dir = opendir($path)) {
        while (($file = readdir($dir)) !== false) {
            $fullPath = $path.$file;

            // Check file
            if (!is_file($fullPath) || !is_readable($fullPath)) {
                continue;
            }

            // You are required to only examine files with the .log suffix, any other files or directories in this folder must be ignored.
            if (isLogFile($fullPath)) {
                $logs[] = $fullPath;
            }
        }

        closedir($dir);
    }

    return $logs;
}

/**
 * Parse a log file and return statistics about it
 *
 * @param string $logFile The path to the log file
 * @return array|false An array of the statistics generated
 */
function parseLogFile($logFile) {
    $handle = fopen($logFile, 'r');

    if (!$handle) {
        return false;
    }

    // 1. The total number of file requests in the month.
    $totalRequests = 0;

    // 2. The number of file requests from the articles directory.
    $articleRequests = 0;

    // 3. The TOTAL bandwidth consumed by the file requests over the month.
    $totalBandwidth = 0;

    // 4. The number of requests that resulted in 404 status errors.
    $totalNotFound = 0;

    // 4. Display a list of the filenames that produced these 404 errors
    $filesNotFound = array();

    while (feof($handle) === false) {
        $line = fgets($handle);

        if (strlen($line) === 0) {
            continue;
        }

        $filename = parseFilename($line);
        $statusCode = parseStatusCode($line);
        $bandwidth = parseBandwidth($line);

        if ($statusCode == 404) {
            $totalNotFound++;
            $filesNotFound[] = $filename;
        }

        if (strpos($filename, 'articles/') === 0) {
            $articleRequests += 1;
        }

        $totalBandwidth += $bandwidth;
        $totalRequests++;
    }

    fclose($handle);

    // 4. (try not to repeat filenames if the same wrong filename was requested more than once)
    $filesNotFound = array_unique($filesNotFound);

    return array(
        'month'           => parseMonthName($logFile),
        'totalRequests'   => $totalRequests,
        'articleRequests' => $articleRequests,
        'totalBandwidth'  => $totalBandwidth,
        'totalNotFound'   => $totalNotFound,
        'filesNotFound'   => $filesNotFound,
    );
}


/**
 * Parse month name from path
 *
 * @param string $path
 * @return string
 */
function parseMonthName($path) {
    return ucfirst(
        pathinfo($path, PATHINFO_FILENAME)
    );
}


/**
 * Parse an requested file from an access log
 *
 * @param string $line The line from an access log
 * @return string The file requested parsed from the line
 */
function parseFilename($line) {
    $startOfRequest = strpos($line, '"');
    $endOfMethod = strpos($line, ' ', $startOfRequest);
    $endOfFilename = strpos($line, ' ', $endOfMethod + 1);

    $filename = substr($line, $endOfMethod + 1, $endOfFilename - $endOfMethod - 1);

    return $filename;
}

/**
 * Parse an HTTP status code from an access log
 *
 * @param string $line The line from an access log
 * @return string The HTTP status code parsed from the line
 */
function parseStatusCode($line) {
    $endOfDate = strpos($line, ']') + 2;
    $startOfRequest = strpos($line, '"') - 1;

    $statusCode = substr($line, $endOfDate, $startOfRequest - $endOfDate);

    return $statusCode;
}


/**
 * Parse an bandwidth in bytes from an access log
 *
 * @param string $line The line from an access log
 * @return string The bandwidth in bytes parsed from the line
 */
function parseBandwidth($line) {
    $startOfBandwidth = strpos($line, '"', strpos($line, '"') + 1) + 2;
    $endOfBandwidth = strpos($line, ' ', $startOfBandwidth);

    $bandwidth = substr($line, $startOfBandwidth, $endOfBandwidth - $startOfBandwidth);

    return $bandwidth;
}
