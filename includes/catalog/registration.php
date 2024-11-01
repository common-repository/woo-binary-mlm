<?php
class BMW_Registration
{
    use Letscms_BMW_CommonClass;
    public function __construct()
    {
    }
    public function register_user()
    {

        $error = array();
        $var = 'check';
        global $wpdb;
        global $current_user;
        $current_user = wp_get_current_user();
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";


        if (!empty($_REQUEST['sp']) && isset($_REQUEST['sp'])) {

            $sp = sanitize_text_field($_REQUEST['sp']);
            $sp_name = $wpdb->get_var("select u.user_login from {$wpdb->prefix}users as u,{$wpdb->prefix}bmw_users as bmw where bmw.user_key='" . $sp . "' AND u.Id=bmw.user_id");
            if ($sp_name) {
?>
                <script type='text/javascript'>
                    jQuery.cookie('sp_name', '<?php echo $sp_name ?>', {
                        path: '/'
                    });
                </script>
            <?php
            }
        } else {

            $sp_name = empty($_COOKIE["sp_name"]) ? '' : $_COOKIE["sp_name"];
        }
        //print_r($_COOKIE);
        /****** code to save cookie sp_name ****/


        if (is_user_logged_in()) {
            $id = $current_user->ID;
            $user_meta = get_userdata($id);
            $user_roles = $user_meta->roles;

            if (in_array('bmw_user', $user_roles, true)) {
                $sponsor_name = $current_user->user_login;
                $readonly_sponsor = 'readonly';
            }
        } else if (isset($_REQUEST['sp']) &&  sanitize_text_field($_REQUEST['sp']) != '') {


            $sponsorName = sanitize_text_field($_REQUEST['sp']);
            if (isset($sponsorName) && $sponsorName != '') {
                $readonly_sponsor = 'readonly';
                $sponsor_name = $sponsorName;
            } else {
                // redirectPage(home_url(), array());
                exit;
            }
        } else {
            $readonly_sponsor = '';
        }

        //most outer if condition
        if (isset($_POST['submit'])) {
            global $wpdb;
            $firstname = sanitize_text_field($_POST['firstname']);
            $lastname = sanitize_text_field($_POST['lastname']);
            $username = sanitize_text_field($_POST['username']);
            $password = sanitize_text_field($_POST['password']);
            $confirm_pass = sanitize_text_field($_POST['confirm_password']);
            $email = sanitize_email($_POST['email']);
            $address1 = sanitize_text_field($_POST['address1']);
            $sponsor = sanitize_text_field($_POST['sponsor']);
            $city = sanitize_text_field($_POST['city']);
            $state = sanitize_text_field($_POST['state']);
            $postalcode = sanitize_text_field($_POST['postalcode']);
            $dob = sanitize_text_field($_POST['dob']);


            if (empty($username)) {
                $error['err_username'] = __('User Name could not be empty', 'bmw');
            }

            if (empty($firstname)) {

                $error['err_firstname'] = __('First Name could not be empty', 'bmw');
            }

            if (empty($lastname)) {
                $error['err_lastname'] = __('Last Name could not be empty', 'bmw');
            }

            if (strlen($password) < 6) {
                $error['err_password'] = __('Password is invalid', 'bmw');
            }

            if ($password != $confirm_pass) {
                $error['err_confirm_password'] = __('Password does not Match', 'bmw');
            }

            if (empty($email)) {
                $error['err_email'] = __('Email could not be empty', 'bmw');
            } else if (!is_email($email)) {
                $error['err_email'] = __("E-mail address is invalid.", 'bmw');
            } else if (email_exists($email)) {
                $error['err_email'] = __("E-mail address is already in use.", 'bmw');
            }

            if (empty($sponsor)) {
                $error['err_sponsor'] = __('Sponsor could not be empty', 'bmw');
            }

            if (empty($address1)) {
                $error['err_address1'] = __('Address could not be empty', 'bmw');
            }

            if (empty($city)) {
                $error['err_city'] = __('City could not be empty', 'bmw');
            }

            if (empty($state)) {
                $error['err_state'] = __('State could not be empty', 'bmw');
            }

            if (empty($postalcode)) {
                $error['err_postalcode'] = __('Postalcode could not be empty', 'bmw');
            }

            if (empty($dob)) {
                $error['err_dob'] = __('Date of Birth could not be empty', 'bmw');
            }

            if (isset($_GET['l']) && $_GET['l'] != '')
                $leg = $_GET['l'];
            else
                $leg = sanitize_text_field($_POST['leg']);

            if ($leg != '0') {
                if ($leg != '1') {
                    $error['err_leg'] = __("You have enter a wrong placement", 'bmw');
                }
            }
            //generate random numeric key for new user registration
            $user_key = $this->letscms_generateKey();
            //if generated key is already exist in the DB then again re-generate key
            do {
                global $wpdb;
                $sql = "SELECT COUNT(*) ck FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $user_key . "'";

                $check = $wpdb->get_var($sql);

                $flag = 1;
                if ($check == 1) {
                    $user_key = $this->letscms_generateKey();
                    $flag = 0;
                }
            } while ($flag == 0);

            //check parent key exist or not
            if (isset($_GET['k']) && $_GET['k'] != '') {
                if (!$this->checkKey($_GET['k']))
                    $error[] = __("Parent key does not exist", 'bmw');
                // check if the user can be added at the current position
                $checkallow = $this->checkallowed($_GET['k'], $leg);
                if ($checkallow >= 1)
                    $error['err_leg'] = __("You have enter a wrong placement", 'bmw');
            }
            // outer if condition
            if (empty($error)) {
                global $wpdb;
                // inner if condition
                $sponsor = $this->getSponsorKeyBySponsorname(sanitize_text_field($_REQUEST['sponsor']));
                $sponsor_key = $this->getSponsorKeyBySponsorname(sanitize_text_field($_REQUEST['sponsor']));


                if ($sponsor_key != '') {
                    //find parent key
                    if (isset($_GET['k']) && $_GET['k'] != '') {
                        $p_key = $_GET['k'];
                        if ($this->checkValidParentKey($p_key)) {
                            $parent_key = $p_key;
                        } else {
                            $error['err_parent'] = __("\n Invalid Parent Key", 'bmw');
                        }
                    } else {
                        $sponsor_key =  $sponsor_key;
                        do {
                            $sql = "SELECT `user_key` FROM {$wpdb->prefix}bmw_users WHERE parent_key = '" . $sponsor_key . "' AND 
                          leg = '" . $leg . "'";

                            $parentquery = $wpdb->get_row($sql);

                            $num = $wpdb->num_rows;
                            if ($num) {
                                $sponsor_key = $parentquery->user_key;
                            }
                        } while ($num == 1);
                        $parent_key = $sponsor_key;
                    }


                    $user = array(
                        'user_login' => $username,
                        'user_pass' => $password,
                        'first_name' => $firstname,
                        'last_name' => $lastname,
                        'user_email' => $email,
                        'role'    => 'bmw_user'
                    );

                    // return the wp_users table inserted user's ID
                    $user_id = wp_insert_user($user);
                    //print_r($user_id); die;
                    if (empty($user_id->errors)) {
                        $unique = TRUE;

                        //insert the registration form data into user_meta table
                        add_user_meta($user_id, 'user_address1', $address1, $unique);
                        add_user_meta($user_id, 'user_city', $city, $unique);
                        add_user_meta($user_id, 'user_state', $state, $unique);
                        add_user_meta($user_id, 'user_postalcode', $postalcode, $unique);
                        add_user_meta($user_id, 'user_dob', $dob, $unique);

                        /*Send e-mail to admin and new user - 
        You could create your own e-mail instead of using this function*/
                        wp_new_user_notification($user_id);
                        // echo $user_id.'<br>';
                        // echo $user_key.'<br>';
                        // echo $parent_key.'<br>';
                        // echo $sponsor.'<br>';
                        // echo $leg.'<br>';
                        // die;
                        //insert the data into {$wpdb->prefix}bmw_users table
                        $insert = "INSERT INTO {$wpdb->prefix}bmw_users
                (
                user_id, user_key, parent_key, sponsor_key, leg, 
                payment_status, qualification_point, left_point,right_point,own_point,
                created_at,paid_at
                ) 
                VALUES
                (
                  '" . $user_id . "','" . $user_key . "', '" . $parent_key . "', '" . $sponsor . "', '" . $leg . "',
                  '0','0','0','0','0','" . date('Y-m-d H:i:s') . "',''
                  
                )";


                        // if all data successfully inserted
                        if ($wpdb->query($insert)) { //begin most inner if condition

                            //entry on left leg and Right leg
                            if ($leg == 0) {
                                $insert = "INSERT INTO {$wpdb->prefix}bmw_leftleg (`id`, `parent_key`,`user_key`,`sponsor_key`,`comm_status`) VALUES ('', '" . $parent_key . "','" . $user_key . "','" . $sponsor . "','0')";
                                $insert = $wpdb->query($insert);
                            } else if ($leg == 1) {
                                $insert = "INSERT INTO {$wpdb->prefix}bmw_rightleg (`id`, `parent_key`,`user_key`,`sponsor_key`,`comm_status`) VALUES ('', '" . $parent_key . "','" . $user_key . "','" . $sponsor . "','0')";
                                $insert = $wpdb->query($insert);
                            }

                            while ($parent_key != '0') {
                                $sql = "SELECT `parent_key`, `leg` FROM {$wpdb->prefix}bmw_users WHERE `user_key` = '" . $parent_key . "'";
                                $result = $wpdb->get_row($sql);
                                $num_rows = $wpdb->num_rows;

                                if ($num_rows) {
                                    if ($result->parent_key != '0') {
                                        if ($result->leg == 1) {
                                            $insert = "INSERT INTO {$wpdb->prefix}bmw_rightleg (`id`,`parent_key`,`user_key`,`sponsor_key`,`comm_status`) VALUES ('','" . $result->parent_key . "','" . $user_key . "','" . $sponsor . "','0')";
                                            $insert = $wpdb->query($insert);
                                        } else {
                                            $insert = "INSERT INTO {$wpdb->prefix}bmw_leftleg (`id`, `parent_key`,`user_key`,`sponsor_key`,`comm_status`) 
                  VALUES ('','" . $result->parent_key . "','" . $user_key . "', '" . $sponsor . "','0')";
                                            $insert = $wpdb->query($insert);
                                        }
                                    }
                                    $parent_key = $result->parent_key;
                                } else {
                                    $parent_key = '0';
                                }
                            }

                            $var = '';

                            $general = get_option('bmw_general_settings');
                            if (!empty($general['letscms_reg_url'])) {
                                $letscms_reg_url = $general['letscms_reg_url'];
                                //wp_redirect($url);
                                $url = get_bloginfo('url') . "/" . $letscms_reg_url;
                                echo "<script>window.location='$url'</script>";
                            } else {
                                echo "<script>window.location='my-downlines'</script>";
                                // $msg= "<span class="error_weight" style='color:green;'>".__('Congratulations! You have successfully registered.', 'bmw')."</span><br /><br />
                                //   <p>".__('Your Member Id is', 'bmw')." : ".$user_key."</p>
                                //   <p style='font-size:18px; font-weight:bold;'><a href='".wp_login_url()."'>
                                //   ".__('Click here to continue to login', 'bmw')."</a></p>";
                            }
                        } //end most inner if condition
                    } else {
                        foreach ($user_id->errors as $key => $value) {
                            foreach ($value as $val) {
                                $error[] = $val;
                            }
                        }
                    }
                } else {

                    $error['err_sponsor'] = __("\n Invalid Sponsor", 'bmw');
                }
            } //end outer if condition
        } //end most outer if condition

        // if any error occoured
        // if (!empty($error)) {
        //     foreach ($error as $er) {
        //         echo '<div style="color:red;margin-top:0px;margin-bottom:0px;">' . $er . '</div>';
        //     }
        // }

        if (isset($_POST['leg']) && sanitize_text_field($_POST['leg']) == '0') {
            $checked = 'checked';
        } else if (isset($_GET['l']) && $_GET['l'] == '0') {
            $checked = 'checked';
            $disable_leg = 'disabled';
        } else
            $checked = '';

        if (isset($_POST['leg']) && sanitize_text_field($_POST['leg']) == '1') {
            $checked1 = 'checked';
        } else if (isset($_GET['l']) && $_GET['l'] == '1') {
            $checked1 = 'checked';
            $disable_leg = 'disabled';
        } else
            $checked1 = '';

        if ($var != '') { ?>
            <div class="container" style="  box-shadow: 0px 0px 10px 5px gainsboro;">
                <form name="bmw_signup" id="bmw_signup" method="post" action="">
                    <div class="tab-content" id="myTabContent">
                        <h3 class="register-heading pt-4">Registration Form</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">User Name *</label>
                                    <input type="text" class="form-control" name="username" placeholder="User Name *" id="letscms_username" maxlength="20" size="37" value="<?php echo isset($_POST['username']) ? htmlentities(sanitize_text_field($_POST['username'])) : '' ?>" onBlur="checkUserNameAvailability(<?php echo $adminajax; ?>,this.value);">
                                    <!-- <div id="check_user"></div> -->
                                    <span style="font-size:12px; font-style:italic; color:#666666">User name must be 5 character, at least one number and alphabet. </span><br>
                                    <span class="error_weight" id="err_username" style="color:red"><?php echo (isset($error['err_username']) && !empty($error['err_username'])) ? $error['err_username'] : '' ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">First Name *</label>
                                    <input class="form-control" type="text" name="firstname" placeholder="First Name *" id="letscms_firstname" value="<?php echo isset($_POST['firstname']) ? htmlentities(sanitize_text_field($_POST['firstname'])) : '' ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'firstname');">
                                    <div id="check_firstname"></div><span class="error_weight" id="err_firstname" style="color:red"><?php echo (isset($error['err_firstname']) && !empty($error['err_firstname'])) ? $error['err_firstname'] : '' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Last Name *</label>

                                    <input class="form-control" type="text" name="lastname" placeholder="Last Name *" id="letscms_lastname" value="<?php echo isset($_POST['lastname']) ? htmlentities(sanitize_text_field($_POST['lastname'])) : '' ?>" maxlength="20" size="37" onBlur="return checkname(this.value, 'lastname');">
                                    <div id="check_lastname"></div><span class="error_weight" id="err_lastname" style="color:red"><?php echo (isset($error['err_lastname']) && !empty($error['err_lastname'])) ? $error['err_lastname'] : '' ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Password *</label>

                                    <input class="form-control" type="password" name="password" placeholder="Password *" id="letscms_password" maxlength="20" size="37" />
                                    <span style="font-size:12px; font-style:italic; color:#666666">Password length at least 6 character</span></br><span class="error_weight" id="err_password" style="color:red"><?php echo (isset($error['err_password']) && !empty($error['err_password'])) ? $error['err_password'] : '' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Confim Password *</label>

                                    <input class="form-control" type="password" name="confirm_password" placeholder="Confim Password *" id="letscms_confirm_password" maxlength="20" size="37"><span class="error_weight" id='message' style="color:red">
                                        <?php echo (isset($error['err_confirm_password']) && !empty($error['err_confirm_password'])) ? $error['err_confirm_password'] : '' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Email Id *</label>

                                    <input class="form-control" type="text" name="email" placeholder="Email Id *" id="letscms_email" value="<?php echo isset($_POST['email']) ? htmlentities(sanitize_text_field($_POST['email'])) : '' ?>" size="37" onBlur="checkEmailAvailability(<?php echo $adminajax; ?>,this.value);">
                                    <div id="check_email" class="error_weight" style="color:red">
                                        <?php echo (isset($error['err_email']) && !empty($error['err_email'])) ? $error['err_email'] : '' ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">DOB *</label>

                                    <input class="form-control dob" type="date" name="dob" placeholder="DOB *" value="<?php echo isset($_POST['dob']) ? htmlentities(sanitize_text_field($_POST['dob'])) : '' ?>" maxlength="20" size="22">
                                </div><span class="error_weight" id="err_dob" style="color:red"><?php echo (isset($error['err_dob']) && !empty($error['err_dob'])) ? $error['err_dob'] : '' ?></span>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Address *</label>

                                    <input class="form-control" type="text" name="address1" placeholder="Address *" id="letscms_address1" value="<?php echo isset($_POST['address1']) ? htmlentities(sanitize_text_field($_POST['address1'])) : '' ?>" size="37"><span class="error_weight" id="err_address" style="color:red"><?php echo (isset($error['err_address1']) && !empty($error['err_address1'])) ? $error['err_address1'] : '' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">City *</label>

                                    <input class="form-control" type="text" name="city" placeholder="City" id="letscms_city" value="<?php echo isset($_POST['city']) ? htmlentities(sanitize_text_field($_POST['city'])) : '' ?>" maxlength="30" size="37"><span class="error_weight" id="err_city" style="color:red"><?php echo (isset($error['err_city']) && !empty($error['err_city'])) ? $error['err_city'] : '' ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">State *</label>

                                    <input class="form-control" type="text" name="state" placeholder="State" id="letscms_state" value="<?php isset($_POST['state']) ? htmlentities(sanitize_text_field($_POST['state'])) : '' ?>" maxlength="30" size="37"><span class="error_weight" id="err_state" style="color:red"><?php echo (isset($error['err_state']) && !empty($error['err_state'])) ? $error['err_state'] : '' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Postal Code *</label>

                                    <input class="form-control" type="text" name="postalcode" placeholder="Postal Code" id="letscms_postalcode" value="<?php echo isset($_POST['postalcode']) ? htmlentities(sanitize_text_field($_POST['postalcode'])) : '' ?>" maxlength="20" size="37" onBlur="return allowspace(this.value,'postalcode');"><span class="error_weight" id="err_postalcode" style="color:red"><?php echo (isset($error['err_postalcode']) && !empty($error['err_postalcode'])) ? $error['err_postalcode'] : '' ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="label-control">Sponsor Name *</label>

                                    <?php
                                    if (isset($sponsor_name) && $sponsor_name != '')
                                        $spon = $sponsor_name;
                                    else if (isset($_POST['sponsor']))
                                        $spon = htmlentities(sanitize_text_field($_POST['sponsor']));
                                    else {
                                        $spon = '';
                                        $readonly_sponsor = '';
                                    }
                                    ?>
                                    <input type="text" class="form-control" name="sponsor" id="sponsor" placeholder="Sponsor Name*" value="<?php echo $spon; ?>" maxlength="20" size="37" onBlur="checkReferrerAvailability(<?php echo $adminajax; ?>,this.value);" <?php echo $readonly_sponsor; ?> />
                                    <span class="error_weight" style="font-size:12px; font-style:italic; color:#666666">Sponsor name</span>
                                    <div id="check_referrer"></div><span class="error_weight" id="err_sponsor" style="color:red"><?php echo (isset($error['err_sponsor']) && !empty($error['err_sponsor'])) ? $error['err_sponsor'] : '' ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="row m-auto">
                            <div class="form-group">
                                <label><?php echo __('Left Leg', 'bmw'); ?></label>
                                <input id="left" type="radio" name="leg" value="0" <?php echo $checked; ?> <?php echo (isset($disable_leg)) ? $disable_leg : ''; ?> required />
                                <label class="ml-3"><?php echo __('Right Leg', 'bmw'); ?></label>
                                <input id="right" type="radio" name="leg" value="1" <?php echo $checked1; ?> <?php echo (isset($disable_leg)) ? $disable_leg : ''; ?> />
                                <span id="err_leg" class="text-danger" style="color:red"><?php echo (isset($error['err_leg']) && !empty($error['err_leg'])) ? $error['err_leg'] : '' ?></span>
                            </div>
                        </div>
                        <div class="row pb-4">
                            <div class="form-group d-block m-auto">
                                <input class="btn btn-primary d-block m-auto w-100" name="submit" value="Register" id="bmw_register" type="submit" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
<?php
        }
    }
}
