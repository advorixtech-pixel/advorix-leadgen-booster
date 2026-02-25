<?php
defined('ABSPATH') || exit;

class Adv_Public_Display {

    public function init() {
        add_action('wp_footer', array($this, 'whatsapp_button'));
    }

    public function whatsapp_button() {
        $number = preg_replace('/\D+/', '', (string) get_option('adv_whatsapp_number', '1234567890'));
        if (strlen($number) < 7 || strlen($number) > 15) {
            return;
        }

        if (empty($number)) {
            return;
        }

        $url = 'https://wa.me/' . rawurlencode($number) . '?text=' . rawurlencode('Hello');
        echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr__('Chat on WhatsApp (opens in a new tab)', 'advorix-leadgen-booster') . '" id="adv-whatsapp-button">' . esc_html__('Chat on WhatsApp', 'advorix-leadgen-booster') . '</a>';
    }
}
