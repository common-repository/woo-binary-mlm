<?php
class Bmw_First_Registration
{
    use Letscms_BMW_CommonClass;
    public function register_first_user()
    {
        global $wpdb;
        $error = array();
        $msg = '';
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";
        if (!empty($_REQUEST['bmw_first_user'])) {
            $bmw_first_user = sanitize_text_field($_REQUEST['bmw_first_user']);
        }

        if (isset($bmw_first_user)) {
            $username = sanitize_text_field($_POST['username']);
            $password = sanitize_text_field($_POST['password']);
            $confirm_pass = sanitize_text_field($_POST['confirm_password']);
            $email = sanitize_email($_POST['email']);

            //User Name that is not to be used.
            $invalid_usernames = array('admin');

            //username validation
            $username = sanitize_user($username);

            if (!validate_username($username) || in_array($username, $invalid_usernames))
                $error[] = __("Username is invalid.", 'bmw');


            if (username_exists($username))
                $error[] = __("Username already exists.", 'bmw');


            if ($this->checkInputField($password))
                $error[] = __("Please enter your password.", 'bmw');

            if ($this->confirmPassword($password, $confirm_pass))
                $error[] = __("Please confirm your password.", 'bmw');

            //Do e-mail address validation
            if (!is_email($email))
                $error[] = __("E-mail address is invalid.", 'bmw');

            if (email_exists($email))
                $error[] = __("E-mail address is already in use.", 'bmw');

            $user_key = $this->letscms_generateKey();

            if (empty($error)) {
                $user = array(
                    'user_login' => $username,
                    'user_pass' => $password,
                    'user_email' => $email,
                    'role'        => 'bmw_user'
                );

                $user_id = wp_insert_user($user);

                /*Send e-mail to admin and new user - */
                wp_new_user_notification($user_id, null,  $notify = 'both');

                //insert the data into {$wpdb->prefix}bmw_users table
                $insert = "INSERT INTO {$wpdb->prefix}bmw_users
						    (
								user_id, user_key, parent_key, sponsor_key, leg,
								payment_status, qualification_point, left_point,right_point,own_point,
								created_at,paid_at
							)
							VALUES
							(
								'" . $user_id . "','" . $user_key . "', '0', '0', '0',
								'1','0','0','0','0',
								'" . date('Y-m-d H:i:s') . "',''
							)";

                if ($wpdb->query($insert)) {
                    update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
                    update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));


                    echo "<script type='text/javascript'>window.location='" . $_SERVER['PHP_SELF'] . "?page=" . $_REQUEST['page'] . "&status=ok&mid=" . $user_key . "';</script>";
                }
            } //end outer if condition
        }

?>

        <div class='wrap'>
            <div id="icon-options-general" class="icon32"></div>
            <?php if (!empty($error)) : ?>
                <div class="error settings-error notice is-dismissible">
                    <p> <strong><?php _e('Please Correct the following Error(s):', 'mlm'); ?><br /></strong>
                        <?php foreach ($error as $er) {
                            echo $er . "</br>";
                        } ?></p>
                </div>
            <?php endif ?>

            <form name="frm" id="bmw_frm" method="post" action="" onSubmit="">
                <div style="text-align:left;">
                    <table class="form-table ">
                        <thead>
                            <tr>
                                <th colspan="2"><?php echo __('Register First User of the Network.', 'bmw'); ?></th>
                            </tr>
                        </thead>
                        <tr>
                            <th scope="row"><?php echo __('User Name', 'bmw'); ?> :<span style="color:red">&nbsp;*</span></th>
                            <td><input name="username" class="regular-text" id="username" type="text" value="" size="30" maxlength="20" class="regular-text" onBlur="checkUserNameAvailability(<?php echo $adminajax; ?>,this.value);" />
                                <div id="check_user"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('Password', 'bmw'); ?> :<span style="color:red">&nbsp;*</span></th>
                            <td><input name="password" class="regular-text" type="password" id="letscms_password" value="" size="30" maxlength="50" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('Confirm Password', 'bmw'); ?> :<span style="color:red">&nbsp;*</span></th>
                            <td><input name="confirm_password" class="regular-text" type="password" id="letscms_confirm_password" value="" size="30" maxlength="50" class="regular-text" /><span id="message1"></span></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('Email Id', 'bmw'); ?> :<span style="color:red">&nbsp;*</span></th>
                            <td><input name="email" type="text" class="regular-text" value="" size="30" maxlength="100" class="regular-text" onBlur="checkEmailAvailability(<?php echo $adminajax; ?>,this.value);" />
                                <div id="check_email"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('First Name', 'bmw'); ?> :</th>
                            <td><input name="first_name" type="text" class="regular-text" value="" size="30" maxlength="50" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo __('Last Name', 'bmw'); ?> :</th>
                            <td><input name="last_name" type="text" class="regular-text" value="" size="30" maxlength="100" class="regular-text" /></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <input type="submit" name="bmw_first_user" id="bmw_first_user" value="<?php echo __('Submit', 'bmw') ?>" class='button-primary'>
                            </td>
                        </tr>

                    </table>
                </div>
            </form>

        </div>
<?php
    }
}
