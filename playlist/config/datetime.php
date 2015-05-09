<?php
/*
    Playlist Start Date & Time
 */
$date       = "2015-05-07";
$time       = "20:33:18";

// Create DateTime Objects
$datestart  = date_create($date . 'T' . $time, timezone_open('America/New_York'));
$datetrack  = date_create($date . 'T' . $time, timezone_open('America/New_York'));
$datecheck  = date_create($date . 'T' . $time, timezone_open('America/New_York'));
$datenow    = date_create('now', timezone_open('America/New_York'));