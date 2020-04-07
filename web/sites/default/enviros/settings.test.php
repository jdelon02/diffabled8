<?php

global $config;

$config['system.performance']['cache']['page']['use_internal'] = TRUE;
$config['system.performance']['css']['preprocess'] = TRUE;
$config['system.performance']['css']['gzip'] = TRUE;
$config['system.performance']['js']['preprocess'] = TRUE;
$config['system.performance']['js']['gzip'] = TRUE;
$config['system.performance']['response']['gzip'] = TRUE;
$config['views.settings']['ui']['show']['sql_query']['enabled'] = FALSE;
$config['views.settings']['ui']['show']['performance_statistics'] = FALSE;
$config['system.logging']['error_level'] = 'none';

// Theme rebuild on every page load.
$config['devel.settings']['rebuild_theme'] = FALSE;

$config['environment_indicator.indicator']['bg_color'] = '#e99e01'; // yellow';
$config['environment_indicator.indicator']['fg_color'] = '#ffffff'; //white
