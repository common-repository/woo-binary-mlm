<?php
class Bmw_Mapping
{
    public function bmw_setting_mapping()
    {
        global $wpdb;
        if (isset($_REQUEST['bmw_mapping_settings'])) {
            if (!empty(sanitize_text_field($_POST['letscms_woocommerce_payment']))) {
                $post_data = array();
                foreach ($_POST as $key => $value) {
                    $post_data[$key] = sanitize_text_field($value);
                }
                update_option('bmw_mapping_settings', $post_data);
                $url = get_bloginfo('url') . "/wp-admin/admin.php?page=dashboard-page&tab=eligibility";
                $message = __("You have successfully mapped your plugin order status with woocommerce.", 'bmw');
                echo '<div class="updated settings-error notice is-dismissible"><p>' . $message . '</p></div>';
            } else {
                $error = __("Please select order status.", 'bmw');
                echo '<div class="error settings-error notice is-dismissible"><p>' . $error . '</p></div>';
            }
        } ?>

        <div class='wrap'>
            <div id="icon-options-general" class="icon32"></div>

            <div id="mapping-form">
                <form name="frm" id="bmw_frm" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" onsubmit="">
                    <?php wp_nonce_field(); ?>
                    <table class="form-table">
                        <tr>
                            <th colspan="1" scope="row"><?php echo __('Order Completion Status:', 'bmw'); ?></th>
                            <td>
                                <?php
                                // For woocommerce mapping with the order status
                                $mapping = get_option('bmw_mapping_settings');
                                $order = wc_get_order_statuses();

                                echo '<select name="letscms_woocommerce_payment" >';
                                echo '<option value="">Select Order Status</option>';
                                foreach ($order as $key => $value) {
                                ?>
                                    <option value="<?php echo $key; ?>" <?php echo ($mapping['letscms_woocommerce_payment'] == $key) ? "selected" : ""; ?>><?php echo $value; ?>
                                    </option>
                                <?php }
                                echo '</select>'; ?></br>
                                <p id="tagline-description" class="description">Select the order status, you want to map with woocommerce order status.</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="bmw_mapping_settings" id="bmw_mapping_settings" value="<?php echo __('Update Options', 'bmw') ?>" class='button-primary'></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
<?php
    }
}
