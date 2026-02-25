<?php
/**
 * Uninstall handler for Advorix LeadGen Booster.
 *
 * @package advorix-leadgen-booster
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options = array(
    'adv_popup_text',
    'adv_whatsapp_number',
    'adv_popup_delay',
    'adv_popup_message',
    'adv_popup_template',
);

foreach ($options as $option_name) {
    delete_option($option_name);
}

global $wpdb;
$table_name = esc_sql($wpdb->prefix . 'adv_leads');

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query("DROP TABLE IF EXISTS `{$table_name}`");