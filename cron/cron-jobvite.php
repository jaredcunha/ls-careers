<?php
/**
 * Cron Script to Sync Jobvite with WordPress Careers Post Type
 *
 *
 * Instructions: Create a cronjob (crontab -e, or through your webhosting panel) that runs this script every 15 minutes,
 * or as server load allows.
 * 
 * @package LivingSocial_Careers
 */
//  Example: */15 * * * * /path/to/wp-content/themes/ls-careers/cron/jobvite.php
require_once( '../classes/class-jobvite.php' );
$run = new Jobvite_Career_Sync();
exit();
