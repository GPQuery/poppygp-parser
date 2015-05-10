<?php
/*
    Playlist File to Import
 */
$filename = 'playlist.m3u';
$filepath = './config/';

// Import Playlist Contents as String
$fstring = file_get_contents($filepath . $filename);