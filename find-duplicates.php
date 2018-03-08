<?php
// The goal of this script is to introduce WP CLI and writing data to a CSV.
// and then solve a trivial problem such as finding duplicate posts using the title field

// Run this script with...
// eval-file wp-content/themes/%themename%/scripts/find-duplicates.php
// Or run this bad boy on pantheon with
// terminus wp %projectname% -- eval-file web/wp-content/themes/%themename%/scripts/find-duplicates.php

// Use the WP uploads directory for writing files
$directoryForUploads = wp_upload_dir();
// HOUSEKEEEEEPING
$masterList = []; $dupes = [];

WP_CLI::log('-------------------------------------------------------');

$stakes = new WP_Query([
    'post_status'       => 'publish',
    'order'             => 'DESC',
    'post_type'         => 'stakeholder',
    'posts_per_page'    => -1
]);

// Grab all the stakes
if ($stakes->have_posts()) {
    while ($stakes->have_posts()) {
        // Grab one little guy at a time to process
        $stakes->the_post();

        $title = html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8');

        // Search master list for existing title.
        if (array_search($title, $masterList)) {
            // If it exists, it's a dupe, flag it
            WP_CLI::log('Found ' . $title);
            $dupes[] = [ $title ];
        } else {
            $masterList[] = $title;
        }
    }
}

// As long as the WP directory is available, we can write to it
if (isset($directoryForUploads['path'])) {
    writeCSV($directoryForUploads['path'], $dupes, 'dupes.csv');
}

// I guess we're done here now
WP_CLI::success('DUN');
WP_CLI::log('-------------------------------------------------------');

// Write everything out to a CSV
// The CSV is expecting an array of arrays to generate the proper rows/columns
function writeCSV($path, $data, $filename) {
    if (!empty($data)) {
        $fp = fopen($path . DIRECTORY_SEPARATOR . $filename, 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($data as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }
}
