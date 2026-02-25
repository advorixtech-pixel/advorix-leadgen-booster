<?php
defined('ABSPATH') || exit;

class Adv_Admin_Leads {

    public function init() {
        add_action('admin_menu', array($this, 'add_leads_page'));
        add_action('admin_post_adv_export_csv', array($this, 'export_csv'));
    }

    public function add_leads_page() {
        add_submenu_page(
            'advorix-leadgen-booster',
            esc_html__('Leads', 'advorix-leadgen-booster'),
            esc_html__('Leads', 'advorix-leadgen-booster'),
            'manage_options',
            'adv-leads',
            array($this, 'leads_page')
        );
    }

    public function leads_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        global $wpdb;
        $table = esc_sql($wpdb->prefix . 'adv_leads');
        $per_page = 20;

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $paged = isset($_GET['paged']) ? absint(wp_unslash($_GET['paged'])) : 1;
        $paged = max(1, $paged);
        $offset = ($paged - 1) * $per_page;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $total_items = (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table}`");

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $leads = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, name, email, date FROM `{$table}` ORDER BY id ASC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $per_page,
                $offset
            )
        );

        $total_pages = max(1, (int) ceil($total_items / $per_page));
        $export_url = wp_nonce_url(admin_url('admin-post.php?action=adv_export_csv'), 'adv_export_csv');

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Captured Leads', 'advorix-leadgen-booster') . '</h1>';
        echo '<p><a href="' . esc_url($export_url) . '" class="button button-primary">' . esc_html__('Export CSV', 'advorix-leadgen-booster') . '</a></p>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>' . esc_html__('ID', 'advorix-leadgen-booster') . '</th><th>' . esc_html__('Name', 'advorix-leadgen-booster') . '</th><th>' . esc_html__('Email', 'advorix-leadgen-booster') . '</th><th>' . esc_html__('Date', 'advorix-leadgen-booster') . '</th></tr></thead><tbody>';

        if ($leads) {
            foreach ($leads as $lead) {
                echo '<tr><td>' . esc_html($lead->id) . '</td><td>' . esc_html($lead->name) . '</td><td>' . esc_html($lead->email) . '</td><td>' . esc_html($lead->date) . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="4">' . esc_html__('No leads found.', 'advorix-leadgen-booster') . '</td></tr>';
        }

        echo '</tbody></table>';

        $pagination_base = add_query_arg(
            array(
                'page'  => 'adv-leads',
                'paged' => '%#%',
            ),
            admin_url('admin.php')
        );

        echo wp_kses_post(
            paginate_links(
                array(
                    'base'      => $pagination_base,
                    'format'    => '',
                    'current'   => $paged,
                    'total'     => $total_pages,
                    'type'      => 'plain',
                    'prev_text' => esc_html__('Previous', 'advorix-leadgen-booster'),
                    'next_text' => esc_html__('Next', 'advorix-leadgen-booster'),
                )
            )
        );

        echo '</div>';
    }

    public function export_csv() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied', 'advorix-leadgen-booster'));
        }

        check_admin_referer('adv_export_csv');

        global $wpdb;
        $table = esc_sql($wpdb->prefix . 'adv_leads');

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQL.InterpolatedNotPrepared,PluginCheck.Security.DirectDB.UnescapedDBParameter
        $leads = $wpdb->get_results("SELECT id, name, email, date FROM `{$table}` ORDER BY id ASC", ARRAY_A);

        nocache_headers();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=advorix-leads.csv');

        $output = fopen('php://output', 'w');
        if (false === $output) {
            wp_die(esc_html__('Could not open output stream.', 'advorix-leadgen-booster'));
        }

        fputcsv($output, array('ID', 'Name', 'Email', 'Date'));

        if ($leads) {
            foreach ($leads as $lead) {
                fputcsv(
                    $output,
                    array(
                        isset($lead['id']) ? (int) $lead['id'] : '',
                        $this->sanitize_csv_field(isset($lead['name']) ? $lead['name'] : ''),
                        $this->sanitize_csv_field(isset($lead['email']) ? $lead['email'] : ''),
                        $this->sanitize_csv_field(isset($lead['date']) ? $lead['date'] : ''),
                    )
                );
            }
        }

        exit;
    }

    private function sanitize_csv_field($value) {
        $value = (string) $value;

        if (preg_match('/^[=\+\-@]/', $value)) {
            return "'" . $value;
        }

        return $value;
    }
}