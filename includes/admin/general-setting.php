<?php
class Bmw_General
{
    use Letscms_BMW_CommonClass;
    public function bmw_setting_general()
    {
        global $wpdb;


        $error = '';
        $message = '';
        if (isset($_REQUEST['bmw_general_settings'])) {
            $letscms_reg_url = sanitize_text_field($_POST['letscms_reg_url']);

            if (isset($_POST['letscms_reg_url']) && sanitize_text_field(empty($_POST['letscms_reg_url']))) {
                // if ($this->checkInputField($letscms_reg_url)) {
                $error = __("Please Fill The URL.", 'bmw');
                // }
            }

            if (empty($error)) {
                $post_data = array();
                foreach ($_POST as $key => $value) {
                    $post_data[$key] = sanitize_text_field($value);
                }
                update_option('bmw_general_settings', $post_data);
                $url = get_bloginfo('url') . "/wp-admin/admin.php?page=dashboard-page&tab=mapping";
                $message = __("Your general settings has been successfully updated.", 'bmw');
                echo '<div class="updated settings-error notice is-dismissible"><p>' . $message . '</p></div>';
            } else {
                echo '<div class="error settings-error notice is-dismissible"><p>' . $error . '</p></div>';
            }
        }
        $settings = get_option('bmw_general_settings');

        $user_meta = get_userdata(16);

        // print_r($user_roles);
?>

        <div class='wrap'>
            <div id="icon-options-general" class="icon32"></div>

            <?php
            // $letscms_purchase_reg = (isset($_POST['letscms_purchase_reg']) ? sanitize_text_field($_POST['letscms_purchase_reg']) : (isset($settings['letscms_purchase_reg']) ? $settings['letscms_purchase_reg'] : ''));
            // $letscms_wp_reg = (isset($_POST['letscms_wp_reg']) ? sanitize_text_field($_POST['letscms_wp_reg']) : (isset($settings['letscms_wp_reg']) ? $settings['letscms_wp_reg'] : ''));
            $letscms_reg_url = (isset($letscms_reg_url) ? $letscms_reg_url : (isset($settings['letscms_reg_url']) ? $settings['letscms_reg_url'] : ''));
            ?>

            <div id="general-form">
                <form name="frm" id="bmw_frm" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" onsubmit="">
                    <?php wp_nonce_field(); ?>
                    <table class="form-table">

                        <!-- <tr>
                            <th scope="row"><?php echo __('Register Users to Binary MLM during checkout', 'bmw'); ?></th>
                            <td>
                                <input type="checkbox" class="regular-text" name="letscms_purchase_reg" id="letscms_purchase_reg" value="1" <?php
                                                                                                                                            //  echo ($letscms_purchase_reg == '1') ? ' checked="checked"' : ''; 
                                                                                                                                            ?> />
                            </td>
                        </tr> -->


                        <tr>
                            <th scope="row" class="admin-setting">
                                <strong><?php echo __('URL of after registration redirect page', 'bmw'); ?><span style="color:red;"></span>:</strong>
                            </th>
                            <td>
                                <?php echo site_url() . '/' ?>
                                <input type="text" class="regular-text" name="letscms_reg_url" value="<?php echo $letscms_reg_url; ?>" /></br>
                                <p id="tagline-description" class="description">Enter the address, you want to redirect your registration page.</p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" name="bmw_general_settings" id="bmw_general_settings" value="<?php echo __('Update Options', 'bmw') ?>" class='button-primary'></td>
                        </tr>


                    </table>
                </form>
            </div>

        </div>

<?php

    }
}
