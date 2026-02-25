jQuery(document).ready(function ($) {
    if (typeof adv_vars === 'undefined') {
        return;
    }

    var $popup = $('#adv-popup');
    var $form = $('#adv-form');
    var $thankyou = $('#adv-thankyou');
    var $submitBtn = $form.find('button[type="submit"]');
    var delayMs = Math.max(0, parseInt(adv_vars.popup_delay, 10) || 0) * 1000;

    if (!$popup.length || !$form.length) {
        return;
    }

    setTimeout(function () {
        $popup.attr('aria-hidden', 'false').fadeIn();
    }, delayMs);

    function closePopup() {
        $popup.attr('aria-hidden', 'true').fadeOut();
    }

    $('#adv-close').on('click', closePopup);

    $(document).on('keydown', function (event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    });

    $form.on('submit', function (event) {
        event.preventDefault();

        var name = $.trim($form.find('input[name="adv_name"]').val());
        var email = $.trim($form.find('input[name="adv_email"]').val());
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name || !emailPattern.test(email)) {
            $thankyou.text(adv_vars.popup_message).show();
            return;
        }

        $submitBtn.prop('disabled', true);

        $.post(adv_vars.ajax_url, {
            action: 'adv_save_lead',
            name: name,
            email: email,
            nonce: adv_vars.nonce
        }).done(function (response) {
            var message = adv_vars.popup_message;
            if (response && response.data && response.data.message) {
                message = response.data.message;
            }

            $form.hide();
            $thankyou.text(message).show();

            setTimeout(function () {
                closePopup();
                $form[0].reset();
                $form.show();
                $thankyou.hide();
            }, 12000);
        }).fail(function () {
            $form.hide();
            $thankyou.text(adv_vars.popup_message).show();
            setTimeout(function () {
                closePopup();
                $form[0].reset();
                $form.show();
                $thankyou.hide();
            }, 12000);
        }).always(function () {
            $submitBtn.prop('disabled', false);
        });
    });
});
