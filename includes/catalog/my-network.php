<?php
class BMW_Network
{
    use Letscms_BMW_CommonClass;

    public function view_network_detail()
    {
        global $wp_query, $current_user, $wpdb;
        $pageId = $wp_query->post->ID;
        $userId = $current_user->ID;
        echo "<h2>" . __('My Network Details', 'bmw') . "</h2>";
        $this->letscms_check_user();
        $total_points = $wpdb->get_row("SELECT SUM(left_point) as 'left' , SUM(right_point) as 'right' , SUM(own_point)as 'own' FROM {$wpdb->prefix}bmw_users WHERE user_id='" . $userId . "'");
        $key = $this->get_current_user_key();
        $userDetail     = $this->GetUserInfoById($userId);
        $payoutArr        = $this->MyPayoutDetails($userId);



?>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title"><strong><?php echo __('Personal Details', 'bmw'); ?> :</strong> </h6>
                <p class="card-text">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="head1"><?php echo __('Title', 'bmw'); ?></th>
                            <th class="head1"><?php echo __('Details', 'bmw'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo __('UserId', 'bmw'); ?></td>
                            <td><?php echo $userDetail['id'] ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('UserKey', 'bmw'); ?></td>
                            <td><?php echo $userDetail['userKey'] ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Name', 'bmw'); ?></td>
                            <td><?php echo $userDetail['name'] ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('Email Id', 'bmw'); ?></td>
                            <td><?php echo $userDetail['email'] ?></td>
                        </tr>

                    </tbody>
                </table>
                </p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title"><strong><?php echo __('Payout Details', 'bmw'); ?> :</strong> </h6>
                <p class="card-text">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo __('Payout Id', 'bmw'); ?></th>
                            <th><?php echo __('Amount', 'bmw'); ?></th>
                            <th><?php echo __('Ponits', 'bmw'); ?></th>
                            <th><?php echo __('Date', 'bmw'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $amount = 0;
                        if (!empty($payoutArr)) {

                            foreach ($payoutArr as $payout) :
                                $amount += $payout['paidAmount'];
                        ?>
                                <tr>
                                    <td><?php echo $payout['payout_id']; ?></td>
                                    <td><?php echo $payout['paidAmount']; ?></td>
                                    <td><?php echo $payout['points']; ?></td>
                                    <td><?php echo $payout['payout_date']; ?></td>
                                </tr>
                            <?php endforeach;
                        } else { ?>
                            <tr>
                                <td colspan="4" class="text-center"> <?php echo __('No Payout Available', 'bmw'); ?></td>
                            </tr>
                        <?php }
                        ?>
                    </tbody>
                    <?php if (!empty($amount)) { ?>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-center"><b>
                                        <?php _e('Total Amount = ', 'bmw');
                                        echo wc_price($amount); ?></b>
                                </td>
                            </tr>
                        </tfoot>
                    <?php } ?>
                </table>
                </p>
            </div>
        </div>
<?php
    }
}
