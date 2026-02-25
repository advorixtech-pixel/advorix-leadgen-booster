<?php
if (!defined('ABSPATH')) {
    exit;
}

class Adv_Popup {

    public function init() {
        add_action('wp_footer', array($this, 'render_popup'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    public function enqueue_assets() {
        wp_enqueue_style('adv-public-css', ADV_PLUGIN_URL . 'assets/css/public.css', array(), ADV_PLUGIN_VERSION);
        wp_enqueue_script('adv-popup-js', ADV_PLUGIN_URL . 'assets/js/popup.js', array('jquery'), ADV_PLUGIN_VERSION, true);

        wp_localize_script('adv-popup-js', 'adv_vars', array(
            'ajax_url'       => admin_url('admin-ajax.php'),
            'popup_delay'    => (int) get_option('adv_popup_delay', 2),
            'popup_message'  => sanitize_text_field((string) get_option('adv_popup_message', 'Thank you! We saved your info.')),
            'popup_text'     => sanitize_text_field((string) get_option('adv_popup_text', 'Subscribe Now!')),
            'popup_template' => $this->get_popup_template(),
            'nonce'          => wp_create_nonce('adv_save_lead'),
        ));
    }

    public function render_popup() {
        if (is_feed() || is_robots() || is_trackback()) {
            return;
        }

        $popup_text    = get_option('adv_popup_text', 'Subscribe Now!');
        $popup_message = get_option('adv_popup_message', 'Thank you! We saved your info.');
        $popup_template = $this->get_popup_template();
        $template_class = ('template2' === $popup_template) ? 'adv-template-2' : 'adv-template-1';
        ?>
        <div id="adv-popup" class="adv-popup <?php echo esc_attr($template_class); ?>" aria-hidden="true">
            <button id="adv-close" type="button" aria-label="<?php esc_attr_e('Close popup', 'advorix-leadgen-booster'); ?>">&times;</button>
            <form id="adv-form">
                <label class="adv-sr-only" for="adv_name"><?php esc_html_e('Your Name', 'advorix-leadgen-booster'); ?></label>
                <input id="adv_name" type="text" name="adv_name" placeholder="<?php esc_attr_e('Your Name', 'advorix-leadgen-booster'); ?>" required>

                <label class="adv-sr-only" for="adv_email"><?php esc_html_e('Email Address', 'advorix-leadgen-booster'); ?></label>
                <input id="adv_email" type="email" name="adv_email" placeholder="<?php esc_attr_e('Email Address', 'advorix-leadgen-booster'); ?>" required>

                <button type="submit"><?php echo esc_html($popup_text); ?></button>
            </form>
            <div id="adv-thankyou" aria-live="polite"><?php echo esc_html($popup_message); ?></div>
        </div>
        <?php
    }

    private function get_popup_template() {
        $allowed_templates = array('template1', 'template2');
        $template = (string) get_option('adv_popup_template', 'template1');

        if (!in_array($template, $allowed_templates, true)) {
            return 'template1';
        }

        return $template;
    }
}
