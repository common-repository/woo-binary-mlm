<?php
class Bmw_Display_Member_Profile
{
    use Letscms_BMW_CommonClass;
    public function bmw_display_member_profile_page()
    {
        $memberId = '';
        if (isset($_REQUEST['user_id'])) {
            $memberId = $_REQUEST['user_id'];
            $this->display_bmw_member_profile_details_page($_REQUEST);
        } else {
            $this->bmw_display_members();
        }
    }

    public function bmw_display_members()
    {
        require_once('member-list-table.php');
        $myListTable = new Bmw_Member_List_Table();
        $myListTable->prepare_items();
        $myListTable->display();
    }

    public function display_bmw_member_profile_details_page($request)
    {
        $memberId = $request['user_id'];

        global $pagenow;
        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'dashboard')
            $current = 'dashboard';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'my-direct')
            $current = 'my-direct';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'my-left')
            $current = 'my-left';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'my-right')
            $current = 'my-right';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'my-consultant')
            $current = 'my-consultant';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'unpaid-members')
            $current = 'unpaid-members';
        else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && $_GET['tab'] == 'my-payout')
            $current = 'my-payout';
        else
            $current = 'dashboard';

        $tabs = array(
            'dashboard'     => 'Dashboard',
            'my-direct'     => 'Direct Group',
            'my-left'         => 'Left Group',
            'my-right'         => 'Right Group',
            'my-consultant' => 'Consultants',
            'unpaid-members' => 'Unpaid Members',
            'my-payout'        => 'Payout',
        );

        $links = array();

        echo '<div id="icon-options-general" class="icon32"><br></div>';
        echo '<div class="wrap">';
        echo '<ul class="subsubsub">';
        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'current' : '';
            echo "<li><a class='" . $class . "' href='?page=dashboard-page&bmw-setting=member-info&tab=$tab&user_id=$memberId'>" . __($name, 'bmw') . "</a></li> |";
        }
        echo '</u>';
        echo '</div>';

        if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'dashboard') {
            $Dashboard = new Bmw_Dashboard();
            $Dashboard->bmw_member_dashboard($_REQUEST);
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'my-direct') {
            $MyDirect = new Bmw_MyDirect();
            $MyDirect->bmw_member_mydirect_group($_REQUEST);
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'my-left') {
            $MyLeft = new Bmw_MyLeft();
            $MyLeft->bmw_member_myleft_group($_REQUEST);
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'my-right') {
            $MyRight = new Bmw_MyRight();
            $MyRight->bmw_member_myright_group($_REQUEST);
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'my-consultant') {
            $MyConsultant = new Bmw_MyConsultant();
            $MyConsultant->bmw_member_myconsultant($_REQUEST);
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'unpaid-members') {
            $UnpaidMembers = new Bmw_UnpaidMembers();
            $UnpaidMembers->bmw_member_unpaid($_REQUEST);
        } else if ($pagenow == 'admin.php' && $_GET['page'] == 'dashboard-page' && isset($_GET['tab']) && $_GET['tab'] == 'my-payout') {
            $MyPayout = new Bmw_MyPayout();
            $MyPayout->bmw_member_mypayout($_REQUEST);
        } else {
            $Dashboard = new Bmw_Dashboard();
            $Dashboard->bmw_member_dashboard($_REQUEST);
        }
    }
}
