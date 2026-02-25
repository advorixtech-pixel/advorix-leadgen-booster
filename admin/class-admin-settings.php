<?php
defined('ABSPATH') || exit;

class Adv_Admin_Settings {

    public function init() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook_suffix) {
        if ('advorix-leadgen-booster_page_adv-settings' !== $hook_suffix) {
            return;
        }

        wp_enqueue_style('adv-admin-css', ADV_PLUGIN_URL . 'assets/css/admin.css', array(), ADV_PLUGIN_VERSION);
    }

    public function register_settings() {
        register_setting('adv_options_group', 'adv_popup_text', array($this, 'sanitize_popup_text'));
        register_setting('adv_options_group', 'adv_whatsapp_number', array($this, 'sanitize_whatsapp_number'));
        register_setting('adv_options_group', 'adv_popup_delay', array($this, 'sanitize_popup_delay'));
        register_setting('adv_options_group', 'adv_popup_message', array($this, 'sanitize_popup_message'));
        register_setting('adv_options_group', 'adv_popup_template', array($this, 'sanitize_popup_template'));
    }

    public function sanitize_popup_text($value) {
        return sanitize_text_field($value);
    }

    public function sanitize_whatsapp_number($value) {
        return substr(preg_replace('/\D+/', '', (string) $value), 0, 15);
    }

    public function sanitize_popup_delay($value) {
        return min(3600, max(0, absint($value)));
    }

    public function sanitize_popup_message($value) {
        return sanitize_text_field($value);
    }

    public function sanitize_popup_template($value) {
        $allowed = array('template1', 'template2');
        return in_array($value, $allowed, true) ? $value : 'template1';
    }

    public function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $popup_text      = get_option('adv_popup_text', 'Subscribe Now!');
        $whatsapp_number = get_option('adv_whatsapp_number', '1234567890');
        $popup_delay     = get_option('adv_popup_delay', 2);
        $popup_message   = get_option('adv_popup_message', 'Thank you! We saved your info.');
        $popup_template  = get_option('adv_popup_template', 'template1');
        $settings_updated = filter_input(INPUT_GET, 'settings-updated', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $is_saved        = 'true' === $settings_updated;
        ?>
        <div class="adv-admin-container">
            <h1><?php esc_html_e('Advorix Leadgen Booster Settings', 'advorix-leadgen-booster'); ?></h1>

            <?php if ($is_saved) : ?>
                <div class="adv-notice-success" role="status">
                    <?php esc_html_e('Settings saved successfully!', 'advorix-leadgen-booster'); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields('adv_options_group'); ?>
                <div class="adv-settings-box">
                    <div class="adv-field">
                        <label for="adv_popup_text"><?php esc_html_e('Popup Text', 'advorix-leadgen-booster'); ?></label>
                        <input id="adv_popup_text" type="text" name="adv_popup_text" value="<?php echo esc_attr($popup_text); ?>">
                    </div>

                    <div class="adv-field">
                        <label for="adv_whatsapp_number"><?php esc_html_e('WhatsApp Number', 'advorix-leadgen-booster'); ?></label>
                        <input id="adv_whatsapp_number" type="text" name="adv_whatsapp_number" value="<?php echo esc_attr($whatsapp_number); ?>" inputmode="numeric">
                    </div>

                    <div class="adv-field">
                        <label for="adv_popup_delay"><?php esc_html_e('Popup Display Delay (seconds)', 'advorix-leadgen-booster'); ?></label>
                        <input id="adv_popup_delay" type="number" name="adv_popup_delay" value="<?php echo esc_attr($popup_delay); ?>" min="0">
                    </div>

                    <div class="adv-field">
                        <label for="adv_popup_message"><?php esc_html_e('Lead Confirmation Message', 'advorix-leadgen-booster'); ?></label>
                        <input id="adv_popup_message" type="text" name="adv_popup_message" value="<?php echo esc_attr($popup_message); ?>">
                    </div>

                    <div class="adv-field">
                        <label for="adv_popup_template"><?php esc_html_e('Popup Template', 'advorix-leadgen-booster'); ?></label>
                        <select id="adv_popup_template" name="adv_popup_template">
                            <option value="template1" <?php selected($popup_template, 'template1'); ?>><?php esc_html_e('Blue Gradient Popup', 'advorix-leadgen-booster'); ?></option>
                            <option value="template2" <?php selected($popup_template, 'template2'); ?>><?php esc_html_e('Minimal White Popup', 'advorix-leadgen-booster'); ?></option>
                        </select>
                    </div>

                    <div class="adv-field">
                        <button type="submit" class="adv-save-btn"><?php esc_html_e('Save Changes', 'advorix-leadgen-booster'); ?></button>
                    </div>
                </div>
            </form>

            <p class="adv-footer-text"><?php esc_html_e('Free version includes 2 popup templates and basic customization.', 'advorix-leadgen-booster'); ?></p>
            <p class="adv-footer-text adv-footer-brand"><?php esc_html_e('Powered by Advorix Technologies', 'advorix-leadgen-booster'); ?></p>
        </div>
        <?php
    }
}