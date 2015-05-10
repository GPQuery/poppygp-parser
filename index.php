<?php
require 'vendor/autoload.php';
use Carbon\Carbon;


/**
 * Simple parser script for m3u and m3u8 playlists.
 */

//require 'config.php';
require 'config/datetime.php';
require 'config/string.php';
require 'config/playlist.php';
require 'config/flags.php';

/*
$filename = 'playlist.m3u';

// Import playlist contents as string
$fstring  = file_get_contents($filename);
*/
// Array conversion
$farray = explode("\n#EXTINF:", $fstring);

// Remove header
array_shift($farray);

// Initialise array for tracks
$tracks = array();

// Iterate
foreach($farray as $key=>$string) {


    /**
     * Use string functions to retrieve duration (seconds)
     */
    $duration = strstr($string, ',', true);
    // ENDOF

    /**
     * Create DateInterval from extracted duration
     */
    $interval = date_interval_create_from_date_string($duration . ' seconds');
    // ENDOF

    /**
     * Use string functions to retrieve video title
     */
    $title = substr(strstr(substr(strstr($string, ','), 1), ':\\', true), 0, -2);
    // ENDOF

    /**
     * Use RegEx to retrieve year (1950 - 2019) from $title
     *
     * @todo    try, catch: retrieve from $path
     */
    $pattern = "(19[56789]\d|20[01]\d)";
    preg_match($pattern, $title, $matches);
    if($matches[0] < 1950) {
        preg_match($pattern, $string, $matches);
    }
    $year     = $matches[0];
    // ENDOF

    /**
     * Use RegEx to retrieve round (01-09; 1-20) from $title
     *
     * @todo    try, catch: retrieve from $path
     */
    $line    = strtr($title, ",.-_[]@", "       ");
    $line    = str_ireplace($removal, '', $line);
    $line    = str_ireplace($year, '', $line);
    $pattern = "([01]\d|[12]\d|\d)";
    preg_match($pattern, $line, $matches);
    if(is_numeric($matches[0])) {
        $round = $matches[0];
    }
    else {
        $round = NULL;
    }
    // ENDOF

    /**
     * Remove all extraneous information from $title
     */
    $extra = array($year, $round);
    $line = str_ireplace(array($year, $round), '', $line);
    // ENDOF

    /*
     * Attempt to extract a country code
     *
    $country = $title;
    foreach($flags as $name=>$flag) {

    }
    */

    /**
     * Format DateTime as string
     */
    $datetime = date_format($datetrack, DATE_ISO8601);
    // ENDOF

    /**
     * Create track array only if in future
     */
    if($datenow <= $datetrack) {

        $track = array(
            'year'      =>  $year,          // Race Year
            'round'     =>  $round,         // Race Round
            'title'     =>  $line,          // Track Title
            'seconds'   =>  $duration,      // Track Length (sec)
            'time'      =>  $datetime,      // Playback DateTime 
            'flag'      =>  $country        // Country Code
        );

        // Remove whitespace from all elements
        $track = array_map('trim', $track);

        $tracks[] = $track;
    }
    // ENDOF
    
    /**
     * Keep track of last past race--this'll be the current track
     */
    else {
        $current = array(
                'year'      =>  $year,          // Race Year
                'round'     =>  $round,         // Race Round
                'title'     =>  $line,          // Track Title
                'seconds'   =>  $duration,      // Track Length (sec)
                'time'      =>  $datetime,      // Playback DateTime 
                'flag'      =>  $country        // Country Code
            );

        // Remove whitespace from all elements
        $current = array_map('trim', $current);
    }
    // ENDOF

    // Add DateInterval to DateTime pointer
    date_add($datetrack, $interval);
}

// Tack the current track onto beginning!
array_unshift($tracks, $current);

// Print JSON_ENCODE object
echo json_encode($tracks, JSON_FORCE_OBJECT);