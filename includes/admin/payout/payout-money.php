<?php

use function PHPSTORM_META\type;

class Bmw_Distribute_Money
{
    use Letscms_BMW_CommonClass;

    public function Payoutmoney($user_key)
    {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmw_point_transaction WHERE user_key = '" . $user_key . "' AND status='0' and payout_id='0'", ARRAY_A);
        return $results;
    }

    public function distribute_money_calculate()
    {
        global $wpdb;

        $bmw_users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}bmw_users WHERE payment_status='1'");

        $total_user_amount = 0;

        // $action = '';
        // !empty($pid) ? $pid : 0;
        // $data = $this->Payoutmoney($pid);

        $adminajax = "'" . admin_url('admin-ajax.php') . "'";
?>
        <table id="data_table">
            <thead>
                <tr>
                    <th scope="col" width="5%"><?php echo __('S.No.', 'bmw'); ?></th>
                    <th scope="col" width="20%"><?php echo __('User Name', 'bmw'); ?></th>
                    <th scope="col" width="10%"><?php echo __('Commission', 'bmw'); ?></th>
                    <th scope="col" width="10%"><?php echo __('Total Commission', 'bmw'); ?></th>
                    <th scope="col" width="15%"><?php echo __('Payable Amount', 'bmw'); ?></th>
                </tr>
            </thead>
            <?php
            $i = 1;

            if (!empty($bmw_users)) {
                foreach ($bmw_users as $val) {

                    if ($this->eligibility($val->user_key, $val->user_id)) {
                        // $pair_comm = $this->Payoutmoney($val->user_key);

                        $pair_comm = $this->get_pair_commission($val->user_id, $val->user_key);


                        $pair_total            =    array_sum(array_column($pair_comm, 'commission_amount'));
                        $total_user_amount += $pair_total;
                        if (!empty($pair_comm)) {  ?>
                            <tr>
                                <td style="width: 6%"><?php echo $i; ?></td>
                                <td><?php echo $this->getUserNameByUserId($val->user_id); ?></td>

                                <td class="let-align-middle">
                                    <table class="let-table let-table-sm let-m-0">
                                        <thead class="let-bg-info">
                                            <tr class="let-text-center let-text-white">
                                                <th><?php _e('Childs Name', 'BMW'); ?></th>
                                                <th><?php _e('Amount', 'BMW'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($pair_comm as $key => $pair) {

                                                $childs = unserialize($pair['childs']); ?>
                                                <tr class="let-text-center">
                                                    <td>
                                                        <ul class="let-list-group">
                                                            <?php if (!empty($childs)) {
                                                                foreach ($childs as $key => $value) {
                                                                    // print_r($child);
                                                            ?>
                                                                    <li><?php echo $this->getUserNameByKey($value); ?></li>
                                                            <?php

                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                    </td>
                                                    <td class="let-align-middle"><?php echo ($pair['commission_amount']); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>

                                </td>
                                <td><?php echo $pair_total; ?> </td>
                                <td><?php echo ($total_user_amount); ?></td>
                            </tr>
                        <?php
                        }   ?>
            <?php $i++;
                    }
                }
            }      ?>
            <tr>
                <td colspan='15'><button type="submit" name="distribute_money" id="distribute_money" class="button-primary" onclick="savingPayoutMoney(<?php echo $adminajax; ?>);" <?php echo (!empty($total_user_amount) > 0) ? '' : 'disabled'; ?>><?php echo __('Distribute Money in the network', 'bmw'); ?></button>
                </td>
            </tr>
        </table>
        <div id="saveMoney"></div>
<?php

        /**************| Close |**********************/
    }
}
