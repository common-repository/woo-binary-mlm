<?php
class Bmw_UnpaidMembers
{
    use Letscms_BMW_CommonClass;
    public function bmw_member_unpaid($request)
    {
        global $wpdb;
        $userId = $request['user_id'];

        $userKey = $this->getKeyByUserId($userId);
        $data = array();
        if (isset($userKey)) {

            $sqlLeft = "SELECT user_key FROM {$wpdb->prefix}bmw_leftleg WHERE parent_key = '" . $userKey . "' ORDER BY id";
            $resultsLeft = $wpdb->get_results($sqlLeft);

            $userIdArr = array();

            if ($wpdb->num_rows > 0) {
                foreach ($resultsLeft as $row) {
                    $leftUserKey = $row->user_key;
                    $userIdArr[] = $this->getUserIdByKey($leftUserKey);
                }
            }

            $sqlRight = "SELECT user_key FROM {$wpdb->prefix}bmw_rightleg WHERE parent_key = '" . $userKey . "' ORDER BY id";
            $resultsRight = $wpdb->get_results($sqlRight);

            if ($wpdb->num_rows > 0) {
                foreach ($resultsRight as $row) {

                    $rightUserKey = $row->user_key;
                    $userIdArr[] = $this->getUserIdByKey($rightUserKey);
                }
            }
            sort($userIdArr);
            //echo "<pre>";print_r($userIdArr); exit;			
            $i = 1;
            if (count($userIdArr) > 0) {
                foreach ($userIdArr as $userRow => $val) {
                    $userDetail = $this->GetUserInfoById($val);
                    if ($userDetail['payment_status'] == 0) {
                        $data[$i]['sno'] = $i;
                        $data[$i]['userlogin'] = $userDetail['userlogin'];
                        $data[$i]['name'] = $userDetail['name'];
                        $data[$i]['userKey'] = $userDetail['userKey'];
                        $data[$i]['email'] = $userDetail['email'];
                        $data[$i]['payment_status'] = $userDetail['payment_status'];
                        $data[$i]['creationDate'] = $userDetail['creationDate'];
                        $i++;
                    }
                }
            } else {

                $data[$i]['sno'] = $i;
                $data[$i]['userlogin'] = '';
                $data[$i]['name'] = __('No Consultant Found', 'bmw');
                $data[$i]['userKey'] = '';
                $data[$i]['email'] = '';
                $data[$i]['payment_status'] = '';
                $data[$i]['creationDate'] = '';
            }
?>

            <div class="clear"></div>
            <div class='wrap'>
                <!--------------------------------------------------------------------------
  Unpaid member Detail
---------------------------------------------------------------------------->
                <table id="data_table">
                    <thead>
                        <tr>
                            <th colspan="6">Unpaid Members Details</th>
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
                        <?php if (isset($data)) {
                            foreach ($data as $member) { ?>
                                <tr class="gradeX">
                                    <td class="text-center"><?php echo $member['userlogin'] ?></td>
                                    <td><?php echo $member['name']  ?></td>
                                    <td><?php echo $member['userKey'] ?></td>
                                    <td><?php echo $member['email'] ?></td>
                                    <td class="center"><?php echo $member['payment_status'] ?></td>
                                    <td class="center"><?php echo $member['creationDate'] ?></td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
<?php

        }
    }
}
