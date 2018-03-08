# Custom scripting on WordPress

Wait, what's custom scripting? Suppose you have 30k posts in your WordPress database and you want to update the meta data for each one. What do you do? Well, you could run a msyql query for all 30k posts and update them. You could write a script in functions.php and let WordPress process it along with everything else...is there a better way?


## WP-CLI eval-file
WP-CLI has a beautiful feature tucked away in it's documentation page called eval-file. The eval-file command lets you run PHP after WordPress has loaded. This lets you write scripts within the WordPress ecosystem. Writing scripts withint he WordPress ecosystem helps you utilize all plugins and custom libraries available.


### The Basics

This repo assumes you know what WP-CLI is and how to use it. It also assumes you know how to write basic WordPress queries and loops to do data manipulation.

I prefer making a separate scripts directory to house all my scripts. Additionally, I end up committing these scripts within my repo because I will inevitably need to reference them again.

This is how you execute a script.
```console
~/projects/scripting » wp eval-file wp-content/themes/%themename%/scripts/find-duplicates.php
```


## Hey, that's neat. Do you have any examples?

This repo will house a collection of scripts to do arbitrary things. Many of them will NOT have extensive error checking because these scripts are generally a one-and-done processes.


##### find-duplicates.php

Chugs through a post type named "stakeholder" and finds the ones which are titled the same and generates a CSV of the dupes.


## Resources

* [WP-CLI](https://developer.wordpress.org/cli/commands/eval-file/)
* [WP Query](https://codex.wordpress.org/Class_Reference/WP_Query)
* [fputcsv](http://php.net/manual/en/function.fputcsv.php) - An easy way to generate CSVs with PHP
