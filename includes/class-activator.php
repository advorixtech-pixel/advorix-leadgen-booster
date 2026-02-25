<?php
defined('ABSPATH') || exit;

class Adv_Activator {
    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'adv_leads';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            KEY email (email)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        add_option('adv_popup_text', 'Subscribe Now!', '', 'no');
        add_option('adv_whatsapp_number', '1234567890', '', 'no');
        add_option('adv_popup_delay', 2, '', 'no');
        add_option('adv_popup_message', 'Thank you! We saved your info.', '', 'no');
        add_option('adv_popup_template', 'template1', '', 'no');
    }
}
