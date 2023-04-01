<?php

/**
 * db_tables.php
 *
 * Config array of database tables used in the application
 */
//$config['db_tables']['environment'] = 'test';
$config['db_tables']['environment'] = 'live';
/*
  |--------------------------------------------------------------------------
  | Live Database Table Names
  |--------------------------------------------------------------------------
  |
  | The live table names for Dynamo and MySQL
  |
 */
$config['db_tables']['live']['dynamo'] = array(
	'daily_price_average' => 'daily_price_average',
	'daily_price_average_archive' => 'daily_price_average_archive',
	'products_trends' => 'products_trends_new',
	'violations' => 'violations_new'
);

$config['db_tables']['live']['mysql'] = array(
	'brand_columns' => 'brand_columns',
	'brand_product' => 'brand_product',
	'brand_product_product' => 'brand_product_product',
	'cms_pages' => 'cms_pages',
	'columns' => 'columns',
	'cron_graph_data' => 'cron_graph_data',
	'cron_log' => 'cron_log',
	'cron_process_stats' => 'cron_process_stats',
	'crowl_merchant_name' => 'crowl_merchant_name_new',
	'crowl_merchant_notes' => 'crowl_merchant_notes',
	'crowl_product_list' => 'crowl_product_list_new',
	'csv_default_columns' => 'csv_default_columns',
	'daily_price_average' => 'daily_price_average',
	'daily_price_average_archive' => 'daily_price_average_archive',
	'email_messages' => 'email_messages',
	'email_reference' => 'email_reference',
	'email_templates' => 'email_templates',
	'global_settings' => 'global_settings',
	'group_products' => 'group_products',
	'groups' => 'groups',
	'marketplaces' => 'marketplaces',
	'news' => 'news',
	'news_messages' => 'news_messages',
	'news_reference' => 'news_reference',
	'notifications_setting' => 'notifications_setting',
	'products' => 'products',
	'products_deleted' => 'products_deleted',
	'products_history' => 'products_history',
	'products_lookup' => 'products_lookup',
	'products_pricing' => 'products_pricing',
	'products_trends' => 'products_trends_new',
	'proxy_ips' => 'proxy_ips',
	'saved_reports' => 'saved_reports',
	'saved_reports_schedule' => 'saved_reports_schedule',
	'screen_shot_stats' => 'screen_shot_stats',
	'screen_shots' => 'screen_shots',
	'shortcuts' => 'shortcuts',
	'store' => 'store',
	'store_smtp' => 'store_smtp',
	'users_store' => 'users_store',
	'users' => 'users',
	'violation_streaks' => 'violation_streaks',
	'violator_notifications' => 'violator_notifications',
	'violator_notification_emails' => 'violator_notification_emails',
	'violator_notification_settings' => 'violator_notification_settings',
	'violator_notifications_history' => 'violator_notifications_history',
    'violator_notification_email_settings' => 'violator_notification_email_settings',//new table for MAP enforcement settings
    'violator_notification_email_templates' => 'violator_notification_email_templates',//new table for MAP enforcement email templates,
    'amazon_violator_email_settings'		=> 'amazon_violator_email_settings'
);

/*
  |--------------------------------------------------------------------------
  | Test Database Table Names
  |--------------------------------------------------------------------------
  |
  | The test table names for Dynamo and MySQL
  |
 */
$config['db_tables']['test']['dynamo'] = array(
	'daily_price_average' => 'test_daily_price_average',
	'products_trends' => 'test_products_trends_new',
	'violations' => 'test_violations_new'
);

$config['db_tables']['test']['mysql'] = $config['db_tables']['live']['mysql'];
// $config['db_tables']['test']['mysql']['store'] = 'store_test';

