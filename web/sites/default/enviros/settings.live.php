<?php




if (php_sapi_name() != 'cli') {
  // Redirect to https://$primary_domain in the Live environment.
  $primary_domain = 'differentandable.org';

  // Redirect to primary_domain.
  if ($_SERVER['HTTP_HOST'] != $primary_domain) {
    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://'. $primary_domain . $_SERVER['REQUEST_URI']);
    exit();
  }

  // Drupal 8 Trusted Host Settings.
  if (is_array($settings)) {
    $settings['trusted_host_patterns'] = array('^'. preg_quote($primary_domain) .'$');
  }
}

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

$config['environment_indicator.indicator']['bg_color'] = 'red';
$config['environment_indicator.indicator']['fg_color'] = '#ffffff'; //white

// Live Mail and Site settings
$config['system.site']['mail'] = 'info@differentandable.com';
$config['system.site']['name'] = 'Different & Able';
//
//
// // Sendgrid setup.
$config['sendgrid_integration.settings']['apikey'] = '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';
// $config['sendgrid_integration.settings']['test_defaults']['to'] = 'admin@example.com';
// $config['sendgrid_integration.settings']['test_defaults']['subject'] = 'Test Email from SendGrid Module';
// $config['sendgrid_integration.settings']['test_defaults']['body']['value'] = 'Test Message for SendGrid.';
// $config['sendgrid_integration.settings']['test_defaults']['body']['format'] = 'plain_text';
// $config['sendgrid_integration.settings']['test_defaults']['from_name'] = '';
// $config['sendgrid_integration.settings']['test_defaults']['to_name'] = '';
// $config['sendgrid_integration.settings']['test_defaults']['reply_to'] = '';
//
// // Mail Safety settings..
// $config['mail_safety.settings']['enabled'] = FALSE;
// $config['mail_safety.settings']['send_mail_to_dashboard'] = FALSE;

// Theme rebuild on every page load.
$config['devel.settings']['rebuild_theme'] = FALSE;

// Google Analytics
$config['google_analytics.settings']['account'] = 'UA-162554967-1';
$config['google_analytics.settings']['visibility']['request_path_mode'] = 0;
$config['google_analytics.settings']['visibility']['request_path_pages'] = "/admin\r\n/admin/*\r\n/batch\r\n/node/add*\r\n/node/*/*\r\n/user/*/*";
$config['google_analytics.settings']['visibility']['user_role_mode'] = 1;
$config['google_analytics.settings']['visibility']['user_role_roles']['administrator'] = 'administrator';