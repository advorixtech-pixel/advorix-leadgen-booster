<?php
defined('ABSPATH') || exit;

class Adv_Admin_Menu {

    public function init() {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets($hook_suffix) {
        $allowed_hooks = array(
            'toplevel_page_advorix-leadgen-booster',
            'advorix-leadgen-booster_page_adv-settings',
            'advorix-leadgen-booster_page_adv-leads',
        );

        if (!in_array($hook_suffix, $allowed_hooks, true)) {
            return;
        }

        wp_enqueue_style('adv-admin-css', ADV_PLUGIN_URL . 'assets/css/admin.css', array(), ADV_PLUGIN_VERSION);
    }

    public function add_menu() {
        add_menu_page(
            esc_html__('Advorix LeadGen Booster', 'advorix-leadgen-booster'),
            esc_html__('Advorix LeadGen Booster', 'advorix-leadgen-booster'),
            'manage_options',
            'advorix-leadgen-booster',
            array($this, 'dashboard'),
            'dashicons-chart-bar',
            56
        );
    }

    public function dashboard() {
        if (!current_user_can('manage_options')) {
            return;
        }

        ?>
        <div class="wrap">
            <div class="adv-dashboard">
                <h1><?php esc_html_e('Welcome to Advorix Leadgen Booster', 'advorix-leadgen-booster'); ?></h1>
                <p><?php esc_html_e('Capture leads, show popups and chat on WhatsApp.', 'advorix-leadgen-booster'); ?></p>
            </div>
            <div class="adv-cards">
                <div class="adv-card">
                    <h2><?php esc_html_e('Leads', 'advorix-leadgen-booster'); ?></h2>
                    <p><?php esc_html_e('View and export captured leads.', 'advorix-leadgen-booster'); ?></p>
                </div>
                <div class="adv-card">
                    <h2><?php esc_html_e('Popup', 'advorix-leadgen-booster'); ?></h2>
                    <p><?php esc_html_e('Configure popup text, delay and design.', 'advorix-leadgen-booster'); ?></p>
                </div>
                <div class="adv-card">
                    <h2><?php esc_html_e('WhatsApp', 'advorix-leadgen-booster'); ?></h2>
                    <p><?php esc_html_e('Set WhatsApp number for live chat.', 'advorix-leadgen-booster'); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
}