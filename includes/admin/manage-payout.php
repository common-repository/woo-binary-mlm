<?php
class Bmw_Display_Payout
{

    public function display_bmw_page_view()
    {
        echo '<style>.list_style
	{
		border: 1px solid #d7d7d7b3;
		text-align: center;
		padding: 5px;
	}
	.wrap_style
	{
		width:10%;
		float:left;
		min-height: 500px;
		max-height:1200px;
		background-color: #b4b4b41a;
		padding: 10px;

	}
	.list_style a:hover
	{
		text-decoration:none;
		
	} 
	.list_style:hover
	{
		background-color:#fff;
	}
	.content_style
	{
		float:left;
		width:85%;
		margin-top:1%;
	}</style>';
        global $pagenow;

        // if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-points')
        //     $current = 'distribute-points';
        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-money')
            $current = 'distribute-money';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'payout-report')
            $current = 'payout-report';
        else
            $current = 'distribute-money';
        // $current = 'distribute-points';

        $tabs = array(
            'distribute-money' => 'Distribute Money',
            'payout-report' => 'Report'
        );

        $links = array();
        echo '<div class="wrap wrap_style">';
        echo '<ul>';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'current' : '';
            echo "<li class='list_style'><a class='" . $class . "' href='?page=dashboard-page&bmw-setting=payout-reports&tab=$tab'>" . __("$name", 'bmw') . "</a></li>";
        }
        echo '</u>';
        echo '</div>';
        echo "<div class='content_style'>";
        // if($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-points')
        // { 
        // 	 	$Distribute_PV = new Bmw_Distribute_PV();
        //   		$Distribute_PV->distribute_points();
        // }	

        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'distribute-money') {
            $Distribute_Money = new Bmw_Distribute_Money();
            $Distribute_Money->distribute_money_calculate();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'payout-report') {
            $Display_Payout_page = new Bmw_Display_Payout_page();
            $Display_Payout_page->bmw_payouts();
        } else {
            $Distribute_PV = new Bmw_Distribute_Money();
            $Distribute_PV->distribute_money_calculate();
        }

        echo '</div>';
    }
}
