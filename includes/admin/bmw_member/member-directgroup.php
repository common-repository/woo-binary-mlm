<?php
class Bmw_MyDirect
{
    use Letscms_BMW_CommonClass;
    public function bmw_member_mydirect_group($request)
    {
        global $wpdb;
        $data = array();
        $userId = $request['user_id'];

        $userKey = $this->getKeyByUserId($userId);
        if (isset($userKey)) {
            $sql = "SELECT user_id, payment_status FROM {$wpdb->prefix}bmw_users
					WHERE sponsor_key = '" . $userKey . "' ORDER BY created_at , user_id";

            $results = $wpdb->get_results($sql);
            $i = 1;
            if ($wpdb->num_rows > 0) {
                foreach ($results as $row) {
                    $userDetail = $this->GetUserInfoById($row->user_id);
                    if ($userDetail['payment_status'] == 0) {
                        $payment_status = "<span style='color:red;'>Unpaid</span>";
                    } else {
                        $payment_status = "<span style='color:green;'>Paid</span>";
                    }
                    $data[$i]['sno'] = $i;
                    $data[$i]['userlogin'] = $userDetail['userlogin'];
                    $data[$i]['name'] = $userDetail['name'];
                    $data[$i]['userKey'] = $userDetail['userKey'];
                    $data[$i]['email'] = $userDetail['email'];
                    $data[$i]['payment_status'] = $payment_status;
                    $data[$i]['creationDate'] = $userDetail['creationDate'];
                    $i++;
                }
            }
        }

?>
        <div class="clear"></div>
        <div class='wrap'>
            <!--------------------------------------------------------------------------
  Direct Group Details
---------------------------------------------------------------------------->
            <table id="data_table">
                <thead>
                    <tr>
                        <th colspan="6"><?php echo __('Direct Group Details', 'bmw'); ?></th>
                    </tr>
                    <tr>
                        <th><?php echo __('User Name', 'bmw'); ?></th>
                        <th><?php echo __('Name', 'bmw'); ?></th>
                        <th><?php echo __('Member Key', 'bmw'); ?></th>
                        <th><?php echo __('E-Mail', 'bmw'); ?></th>
                        <th><?php echo __('Status', 'bmw'); ?></th>
                        <th><?php echo __('Joining Date', 'bmw'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($data) > 0) {
                        foreach ($data as $member) : ?>
                            <tr>
                                <td align="center"><?php echo $member['userlogin'] ?></td>
                                <td><?php echo $member['name']  ?></td>
                                <td><?php echo $member['userKey'] ?></td>
                                <td><?php echo $member['email'] ?></td>
                                <td class="center"><?php echo $member['payment_status'] ?></td>
                                <td class="center"><?php echo $member['creationDate'] ?></td>
                            </tr>
                        <?php endforeach;
                    } else {
                        ?>
                        <tr>
                            <td colspan="6"><?php _e('No Data Found', 'bmw'); ?></td>
                        </tr>
                    <?php
                    }
                    ?>

                </tbody>

            </table>
        </div>
<?php
    }
}
