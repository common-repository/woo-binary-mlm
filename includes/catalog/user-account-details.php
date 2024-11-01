<?php
class BMW_User_Account_Details
{
    use Letscms_BMW_CommonClass;

    public function view_user_account_details()
    {

        global $wpdb;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $this->letscms_check_user();
        if (!is_user_logged_in()) {
            echo $url = site_url('wp-login.php');
            echo "<script>window.location='$url'</script>";
            exit;
        }
        // $this->letscms_check_user();

        if (isset($_POST['submit'])) {
            $error = [];
            $account_holder_name = sanitize_text_field($_POST['account_holder_name']);
            $account_number = sanitize_text_field($_POST['account_number']);
            $bank_name = sanitize_text_field($_POST['bank_name']);
            $ifsc_code = sanitize_text_field($_POST['ifsc_code']);
            $branch = sanitize_text_field($_POST['branch']);

            if (empty($account_holder_name)) {
                $error['err_account_holder_name'] = __('Please enter account holder name', 'bmw');
            }
            if (empty($account_number)) {
                $error['err_account_number'] = __('Please enter account number', 'bmw');
            }
            if (empty($bank_name)) {
                $error['err_bank_name'] = __('Please enter bank name', 'bmw');
            }

            if (empty($ifsc_code)) {
                $error['err_ifsc_code'] = __('Please enter IFSC code', 'bmw');
            }
            if (empty($branch)) {
                $error['err_branch'] = __('Please enter branch', 'bmw');
            }
            if (empty($error)) {

                /* INSERT USER ACCOUNT DETAILS */
                $user_id = $current_user->ID;
                $meta_key = 'account_holder_name' || 'account_number' || 'bank_name' || 'ifsc_code' || 'branch';
                update_user_meta($current_user->ID, 'account_holder_name', $account_holder_name);
                update_user_meta($current_user->ID, 'account_number', $account_number);
                update_user_meta($current_user->ID, 'bank_name', $bank_name);
                update_user_meta($current_user->ID, 'ifsc_code', $ifsc_code);
                update_user_meta($current_user->ID, 'branch', $branch);
                /* RETIVE ALL ACCOUNT DETAILS */
                // var_dump(get_user_meta( $user_id, 'account_holder_name', true ));

            }
        }
        //     $user_meta = get_userdata( $user_id );
        //     //var_dump(get_userdata( $user_id ));
        //    //var_dump($user_meta->roles);
?>
        <h2><?php echo __('Your Account Details :', 'bmw'); ?></h2>
        <form name="user_account_frm" method="post" id="user_acc_details_submit" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <table class="table table-hover">
                <tr>
                    <td><?php echo __('Account Holder Name', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="account_holder_name" id="account_holder_name" maxlength="40" size="37" value="<?php echo get_user_meta($user_id, 'account_holder_name', true); ?>"><br>
                        <span class="bmw_user_err" id="err_account_holder_name"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo __('Account No', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="account_number" id="account_number" maxlength="40" size="37" value="<?php echo get_user_meta($user_id, 'account_number', true); ?>"><br>
                        <span class="bmw_user_err" id="err_account_number"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo __('Bank Name', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="bank_name" id="bank_name" maxlength="40" size="37" value="<?php echo get_user_meta($user_id, 'bank_name', true); ?>"><br>
                        <span class="bmw_user_err" id="err_bank_name"></span>

                    </td>
                </tr>
                <tr>
                    <td><?php echo __('IFSC Code', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="ifsc_code" id="ifsc_code" maxlength="30" size="37" value="<?php echo get_user_meta($user_id, 'ifsc_code', true); ?>"><br>
                        <span class="bmw_user_err" id="err_ifsc_code"></span>
                    </td>
                </tr>
                <tr>
                    <td><?php echo __('Branch', 'bmw'); ?> <span style="color:red;">*</span> :</td>
                    <td>
                        <input type="text" name="branch" id="branch" maxlength="20" size="37" value="<?php echo get_user_meta($user_id, 'branch', true); ?>"><br>
                        <span class="bmw_user_err" id="err_branch"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <center>
                            <button class="btn btn-primary" name="submit" id="user_acc_submit" type="submit"><?php echo  __('Submit', 'bmw') ?> </button>
                        </center>
                    </td>
                </tr>
            </table>
        </form>
<?php
    }
}
