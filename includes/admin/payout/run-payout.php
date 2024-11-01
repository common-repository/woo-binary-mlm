<?php
class Bmw_Run_Payout
{


    public function __construction()
    {
        $adminajax = "'" . admin_url('admin-ajax.php') . "'";
        global $pagenow, $wpdb;


        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-points')
            $current = 'distribute-points';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-money')
            $current = 'distribute-money';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'payout-report')
            $current = 'payout-report';
        else
            $current = 'distribute-points';

        $tabs = array('distribute-points' => 'Distribute Points', 'distribute-money' => 'Distribute Money', 'payout-report' => 'Report');

        $links = array();

        echo '<div class="wrap" style="display: inline-block;">';
        echo '<ul class="subsubsub">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'current' : '';
            echo "<li><a class='" . $class . "' href='?page=dashboard-page&bmw-setting=payout-reports&tab=$tab'>" . __("$name", 'bmw') . "</a></li> |";
        }
        echo '</u>';
        echo '</div>';

        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-points') {
            $Action_ajax = new Bmw_action_ajax();
            $Action_ajax->bmw_distribute_points();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-money') {
            $Action_ajax = new Bmw_action_ajax();
            $Action_ajax->bmw_distribute_money();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'payout-report') {
            $Display_Payout_page = new Bmw_Display_Payout_page();
            $Display_Payout_page->bmw_payouts();
        }

?>
        </div>

<?php

    }
}
