<?php
/*
 * Display MLM Settings page
 */
class Bmw_Display_MLM_Settings
{

    public function bmw_display_mlm_settings_page()
    {
        global $wpdb;
        $sql = "SELECT id FROM {$wpdb->prefix}bmw_users ";
        $results = $wpdb->get_results($sql);

        $num = $wpdb->num_rows;

        if ($num && ($num) >= 1) {

            $this->settingPage();
        } else {
            $this->registerFirstUser();
        }
    }

    public function settingPage()
    {
        global $pagenow;
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
        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'general')
            $current = 'general';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'mapping')
            $current = 'mapping';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'eligibility')
            $current = 'eligibility';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'payout')
            $current = 'payout';

        else
            $current = 'general';

        $tabs = array('general' => 'General', 'mapping' => 'Mapping', 'eligibility' => 'Eligibility', 'payout' => 'Payout');

        $links = array();

        echo '<div class="wrap wrap_style">';
        echo '<ul class="">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'current' : '';
            echo "<li class='list_style'><a  class='" . $class . "' href='?page=dashboard-page&bmw-setting=settings&tab=$tab'>" . __("$name", 'bmw') . "</a></li>";
        }
        echo '</u>';
        echo '</div>';
        echo '<div class="content_style">';

        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'general') {
            $General = new Bmw_General();
            $General->bmw_setting_general();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'eligibility') {
            $Eligibility = new Bmw_Eligibility();
            $Eligibility->bmw_setting_eligibility();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'payout') {
            $Payout = new Bmw_Payout();
            $Payout->bmw_setting_payout();
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'mapping') {
            $Mapping = new Bmw_Mapping();
            $Mapping->bmw_setting_mapping();
        } else {
            $General = new Bmw_General();
            $General->bmw_setting_general();
        }
        echo '</div>';
    }

    public function registerFirstUser()
    {
        include_once(BMW_ABSPATH . '/includes/admin/register-first-user.php');
        $Registration = new Bmw_First_Registration();
        $Registration->register_first_user();
    }
}//end of class
