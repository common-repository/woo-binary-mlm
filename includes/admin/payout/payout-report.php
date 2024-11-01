<?php
class Bmw_Display_Payout_page
{
    use Letscms_BMW_CommonClass;
    public function bmw_payouts()
    {
        global $wpdb;

        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'view_payout_detail' && isset($_REQUEST['payout_id'])) {
            $payout_id = sanitize_text_field($_REQUEST['payout_id']);
            $this->individualPayoutDetail($payout_id);
        } else {
            $this->payoutList();
        }
    }/*end of main function*/


    public function payoutList()
    {
        global $wpdb;
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";
        // $sql = 	"SELECT id, DATE_FORMAT(`date`,'%d %b %Y') as creationDate,DATE_FORMAT(`date`,'%Y%m%d') as dateFormat FROM  {$wpdb->prefix}bmw_payout_master ";
        $sql =     "SELECT * FROM  {$wpdb->prefix}bmw_payout";
        $results = $wpdb->get_results($sql);
        $num = $wpdb->num_rows;
        $data = array();
        $i = 1;
        if ($num > 0) {
            foreach ($results as $val) {
                $data[$i]['id'] = $val->id;
                $data[$i]['date'] = $val->date;
                // $payoutArr = $this->getMembersByPayoutId($val->id);
                $members = $this->getchildsMembersByPayoutId($val->id);
                $data[$i]['members'] = $members;
                $data[$i]['totalAmount'] = $val->commission_amount;
                $i++;
            }
        }
?>

        <script language="javascript" type="text/javascript">
            function showMembers(payoutId, members) {
                if (members > 0) {
                    var href = "<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $_REQUEST['page']; ?>&bmw-setting=payout-reports&tab=payout-report&action=view_payout_detail&payout_id=" + payoutId;
                    window.location = href;
                } else {
                    alert('<?php echo __("There is no member in this pay cycle", 'bmw') ?>');
                    return false;
                }

            }
        </script>

        <div class="wrap">
            <table id="data_table">
                <thead>
                    <tr>
                        <th colspan="5">Payout List</th>
                    </tr>
                    <tr>
                        <th><?php echo __('Payout Id', 'bmw'); ?></th>
                        <th><?php echo __('Payout Date', 'bmw'); ?></th>
                        <th><?php echo __('Members', 'bmw'); ?></th>
                        <th><?php echo __('Amount', 'bmw'); ?></th>
                        <th><?php echo __('View', 'bmw'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($data) > 0) {
                        foreach ($data as $val) {
                    ?>
                            <tr>
                                <td><?php echo $val['id']; ?></td>
                                <td><?php echo $val['date']; ?></td>
                                <td><?php echo $val['members']; ?></td>
                                <td><?php echo $val['totalAmount']; ?></td>
                                <td><span id="align-center"><a href="javascript:void(0);" onclick="showMembers(<?php echo $val['id']; ?>,<?php echo $val['members']; ?>)" ;><img src="<?php echo BMW_URL ?>/assets/images/admin/view.png" alt="View" title="View"></a></span></td>
                            </tr>

                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5'>No Payout has been run.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

        </div>

    <?php
    }

    public function individualPayoutDetail($payout_id)
    {
        global $wpdb;
        $sql =     "SELECT userid, units, commission_amount,id FROM {$wpdb->prefix}bmw_payout WHERE id = " . $payout_id . " ";
        $results = $wpdb->get_results($sql);
        //echo "<pre>"; print_r($results); die;
        $num = $wpdb->num_rows;
        $data = array();
        $i = 1;
        if ($num > 0) {
            foreach ($results as $val) {
                $userId = $val->userid;
                $data[$i]['name'] = get_user_meta($userId, 'first_name', true) . ' ' . get_user_meta($userId, 'last_name', true);
                $data[$i]['childs'] = $this->get_childs_user_by_payout_id($val->id);
                $data[$i]['units'] = $val->units;
                $data[$i]['commission_amount'] = number_format($val->commission_amount, '2', '.', ',');
                $payable_amount = $val->commission_amount;
                $data[$i]['payable_amount'] = number_format($payable_amount, '2', '.', ',');
                $i++;
            }
        }
    ?>

        <div class="wrap">
            <table id="data_table">
                <thead>
                    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $_REQUEST['page']; ?>&bmw-setting=payout-reports&tab=<?php echo $_REQUEST['tab']; ?>"><?php echo __('Back to All Payout List', 'bmw'); ?></a>
                    <tr>
                        <th colspan="8">Payout Details of Payout Id : <?php echo $payout_id ?></th>
                    </tr>
                    <tr>
                        <th><?php echo __('Name', 'bmw'); ?></th>
                        <th><?php echo __('Members', 'bmw'); ?></th>
                        <th><?php echo __('Points', 'bmw'); ?></th>
                        <th><?php echo __('Commission', 'bmw'); ?></th>
                        <th><?php echo __('Payable Amount', 'bmw'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $val) {
                    ?>
                        <tr>
                            <td><?php echo $val['name']; ?></td>
                            <td>
                                <?php if ($val['childs']) {
                                    $users = $val['childs'];
                                    echo "<ul>";
                                    foreach ($users as $user_name) {
                                        echo "<li>";
                                        echo $user_name;
                                        echo "</li>";
                                    }
                                    echo "</ul>";
                                } ?>
                            </td>
                            <td><?php echo $val['units']; ?></td>
                            <td><?php echo $val['commission_amount']; ?></td>
                            <td><?php echo $val['payable_amount']; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
<?php
        die();
    }
}
