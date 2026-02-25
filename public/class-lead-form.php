<?php
defined('ABSPATH') || exit;

class Adv_Lead_Form {

    public function init() {
        add_action('wp_ajax_adv_save_lead', array($this, 'save_lead'));
        add_action('wp_ajax_nopriv_adv_save_lead', array($this, 'save_lead'));
        add_filter('wp_privacy_personal_data_exporters', array($this, 'register_personal_data_exporter'));
        add_filter('wp_privacy_personal_data_erasers', array($this, 'register_personal_data_eraser'));
    }

    public function save_lead() {
        if (!check_ajax_referer('adv_save_lead', 'nonce', false)) {
            wp_send_json_error(array('message' => get_option('adv_popup_message', 'Invalid request.')), 403);
        }

        $request_method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper(sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD']))) : '';
        if ('POST' !== $request_method) {
            wp_send_json_error(array('message' => get_option('adv_popup_message', 'Invalid method.')), 405);
        }

        $name  = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';

        if (empty($name) || empty($email) || !is_email($email)) {
            wp_send_json_error(array('message' => get_option('adv_popup_message', 'Please provide a valid name and email.')), 400);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'adv_leads';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
        $inserted = $wpdb->insert(
            $table,
            array(
                'name'  => $name,
                'email' => $email,
                'date'  => current_time('mysql'),
            ),
            array('%s', '%s', '%s')
        );

        if (false === $inserted) {
            wp_send_json_error(array('message' => get_option('adv_popup_message', 'Something went wrong. Please try again.')), 500);
        }

        wp_send_json_success(array('message' => get_option('adv_popup_message', 'Thank you! We saved your info.')));
    }

    public function register_personal_data_exporter($exporters) {
        $exporters['advorix-leadgen-booster'] = array(
            'exporter_friendly_name' => esc_html__('Advorix LeadGen Booster Leads', 'advorix-leadgen-booster'),
            'callback'               => array($this, 'export_personal_data'),
        );

        return $exporters;
    }

    public function register_personal_data_eraser($erasers) {
        $erasers['advorix-leadgen-booster'] = array(
            'eraser_friendly_name' => esc_html__('Advorix LeadGen Booster Leads', 'advorix-leadgen-booster'),
            'callback'             => array($this, 'erase_personal_data'),
        );

        return $erasers;
    }

    public function export_personal_data($email_address, $page = 1) {
        global $wpdb;
        $email_address = sanitize_email($email_address);

        $table = esc_sql($wpdb->prefix . 'adv_leads');
        $number = 200;
        $page = (int) $page;
        $offset = ($page - 1) * $number;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $leads = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, name, email, date FROM `{$table}` WHERE email = %s ORDER BY id ASC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $email_address,
                $number,
                $offset
            ),
            ARRAY_A
        );

        $data_to_export = array();
        foreach ($leads as $lead) {
            $data_to_export[] = array(
                'group_id'    => 'advorix_leadgen_booster',
                'group_label' => esc_html__('Advorix LeadGen Booster', 'advorix-leadgen-booster'),
                'item_id'     => 'lead-' . (int) $lead['id'],
                'data'        => array(
                    array(
                        'name'  => esc_html__('Name', 'advorix-leadgen-booster'),
                        'value' => isset($lead['name']) ? sanitize_text_field($lead['name']) : '',
                    ),
                    array(
                        'name'  => esc_html__('Email', 'advorix-leadgen-booster'),
                        'value' => isset($lead['email']) ? sanitize_email($lead['email']) : '',
                    ),
                    array(
                        'name'  => esc_html__('Date', 'advorix-leadgen-booster'),
                        'value' => isset($lead['date']) ? sanitize_text_field($lead['date']) : '',
                    ),
                ),
            );
        }

        return array(
            'data' => $data_to_export,
            'done' => count($leads) < $number,
        );
    }

    public function erase_personal_data($email_address, $page = 1) {
        global $wpdb;
        $email_address = sanitize_email($email_address);

        $table = esc_sql($wpdb->prefix . 'adv_leads');
        $number = 200;
        $page = (int) $page;
        $offset = ($page - 1) * $number;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT id FROM `{$table}` WHERE email = %s ORDER BY id ASC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $email_address,
                $number,
                $offset
            )
        );

        $items_removed = false;
        if (!empty($ids)) {
            foreach ($ids as $id) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
                $deleted = $wpdb->delete($table, array('id' => (int) $id), array('%d'));
                if ($deleted) {
                    $items_removed = true;
                }
            }
        }

        return array(
            'items_removed'  => $items_removed,
            'items_retained' => false,
            'messages'       => array(),
            'done'           => count($ids) < $number,
        );
    }
}