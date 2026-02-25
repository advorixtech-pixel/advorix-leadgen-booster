<?php
defined('ABSPATH') || exit;

class Adv_Loader {

    /**
     * @var Adv_Admin_Settings|null
     */
    private $settings;

    public function __construct() {
        // Admin
        require_once ADV_PLUGIN_DIR . 'admin/class-admin-menu.php';
        require_once ADV_PLUGIN_DIR . 'admin/class-admin-settings.php';
        require_once ADV_PLUGIN_DIR . 'admin/class-admin-leads.php';

        // Public
        require_once ADV_PLUGIN_DIR . 'public/class-public-display.php';
        require_once ADV_PLUGIN_DIR . 'public/class-popup.php';
        require_once ADV_PLUGIN_DIR . 'public/class-lead-form.php';
    }

    public function run() {
        // Admin
        if (is_admin()) {
            $admin = new Adv_Admin_Menu();
            $admin->init();

            $this->settings = new Adv_Admin_Settings();
            $this->settings->init();

            add_action('admin_menu', array($this, 'register_settings_submenu'));

            $leads = new Adv_Admin_Leads();
            $leads->init();
        }

        // Public
        $public = new Adv_Public_Display();
        $public->init();

        $popup = new Adv_Popup();
        $popup->init();

        $lead = new Adv_Lead_Form();
        $lead->init();
    }

    public function register_settings_submenu() {
        if (!$this->settings instanceof Adv_Admin_Settings) {
            return;
        }

        add_submenu_page(
            'advorix-leadgen-booster',
            esc_html__('Popup Settings', 'advorix-leadgen-booster'),
            esc_html__('Popup Settings', 'advorix-leadgen-booster'),
            'manage_options',
            'adv-settings',
            array($this->settings, 'settings_page')
        );
    }
}