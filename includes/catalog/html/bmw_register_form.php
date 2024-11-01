<script>
    var $ = jQuery.noConflict();
    $(document).ready(function() {
        if ($('#createaccount_with_mlm').is(":checked")) {
            $('#createaccount_with_mlm').val(1);
            $('.woocommerce_mlm_section').css('display', 'block');
        } else {
            $('#createaccount_with_mlm').val(0);
            $('.woocommerce_mlm_section').css('display', 'none');
        }

        $('#createaccount_with_mlm').click(function() {
            if ($('#createaccount_with_mlm').is(":checked")) {
                $('#createaccount_with_mlm').val(1);
                $('.woocommerce_mlm_section').css('display', 'block');
            } else {
                $('#createaccount_with_mlm').val(0);
                $('.woocommerce_mlm_section').css('display', 'none');
            }

        });
    });
</script><?php $adminajax = "'" . admin_url('admin-ajax.php') . "'";
            $general_settings = get_option('bmw_general_settings');
            if (isset($general_settings['letscms_purchase_reg']) && $general_settings['letscms_purchase_reg'] == 1) {
            ?><div id="bmw-register-form" class="woocommerce-bmw-account-fields">
        <h3><?php _e('MLM Registration', 'bmw'); ?></h3>
        <p class="form-row form-row-wide create-account woocommerce-validated">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount_with_mlm" type="checkbox" name="createaccount_with_mlm" value="0"> <span><?php _e('Create an account With Binary MLM Woocommerce?', 'bmw'); ?></span>
            </label>
        </p>
        <div class="woocommerce_mlm_section" style="display:none;">
            <p class="form-row form-row-wide create-bmw-account" id="bmw_account_sponsor">
                <label for="bmw_sponsor_name" class=""><?php _e('Sponsor Name', 'bmw'); ?> &nbsp;<abbr class="required" title="required">*</abbr></label>
                <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text " name="bmw_sponsor" id="bmw_sponsor" placeholder="" value="" onBlur="checkSponsor(<?php echo $adminajax; ?>,this.value);" />
                    <div id="bmw_checksponsor"></div>
                </span>
            </p>

            <p class="form-row form-row-wide create-bmw-account" id="bmw_account_placement">
                <label for="bmw_placement" class=""><?php _e('Placement', 'bmw'); ?>&nbsp;<abbr class="required" title="required">*</abbr></label>
                <span class="woocommerce-input-wrapper">
                    <input type="radio" name="bmw_placement" value="0" /><?php _e('Left', 'bmw'); ?> &nbsp; <input type="radio" name="bmw_placement" value="1" /><?php _e('Right', 'bmw'); ?>
                </span>
            </p>
        </div>
    </div>
<?php
            }
