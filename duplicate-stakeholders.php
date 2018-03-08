<?php

// Run this script with
// eval-file wp-content/themes/%%themename%%/scripts/duplicate-stakeholders.php zh-cn --url=https://%%sitename%%.pf/fr-pf

global $sitepress;
global $iclTranslationManagement;

WP_CLI::log('Duplicating Stakeholders from ' . $sitepress->get_current_language());
WP_CLI::log('-------------------------------------------------------');

// Make sure we're only on the fr-pf site
if (!$sitepress->get_current_language() == 'fr-pf') {
    WP_CLI::error("Attempting to do work on a language other than fr-pf, exiting!", true);
}

$targetLanguage = null;

// The target language should be the first argument passed in
if (isset($args) && !empty($args)) {
    $targetLanguage = $args[0];
}

if ($targetLanguage) {
    // Loop through, grab each stake, figure out if a duplication exists and dupe em
    $stakesQuery = new WP_Query( [ 'post_type' => 'stakeholder', 'post_status' => 'publish', 'orderby' => 'title', 'posts_per_page' => -1 ] );
    WP_CLI::log('Found ' . $stakesQuery->found_posts . ' posts');
    $counter = 0;
    if ($stakesQuery->have_posts()) {
        while ($stakesQuery->have_posts()) {
            $stakesQuery->the_post();
            // Only find the steaks which require a duplication
            $availableTranslations = $sitepress->get_element_translations( $sitepress->get_element_trid(get_the_ID()) );
            $pfTranslation = array_filter($availableTranslations, function($item) use ($targetLanguage) { return $item->language_code == $targetLanguage; });
            if (empty($pfTranslation)) {
                $result = $iclTranslationManagement->make_duplicate(get_the_ID(), $targetLanguage);
                if ($result != 1) {
                    WP_CLI::log('Added translation for ' . get_the_title());
                    $iclTranslationManagement->reset_duplicate_flag($result);
                }
                $counter++;
            }
        }
    }

    WP_CLI::log('-------------------------------------------------------');
    WP_CLI::success('Finished. Processed ' . $counter . ' stakeholders.');
} else {
    WP_CLI::error("A target language was not passed in, exiting!", true);
}
