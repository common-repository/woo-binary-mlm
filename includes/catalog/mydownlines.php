 <?php
    class BMW_MyDownlines
    {
        use Letscms_BMW_CommonClass;


        public function user_detail($userKey, $username, $position, $sponsorKey, $parentKey, $payment_status)
        {
            $table = '';
            if (!empty($username)) {
                if ($position == 0) {
                    $position = 'Left';
                } else {
                    $position = 'Right';
                }

                $table = '<table id="" class="table table-sm tbl-tooltip"><thead></thead> <tbody><tr><td>User Name:' . $username . '</td></tr><tr><td>User Key:' . $userKey . '</td></tr><tr><td>Sponsor Key:' . $sponsorKey . '</td></tr><tr><td>Position:' . $position . '</td></tr><td>Parent Key:' . $parentKey . '</td></tr><td>Payment Status:' . $payment_status . '</td></tr></tbody></table>';
            } else {
                $table = '<div style="padding-top:70px;"><table id="data_table"><tr><td>You can add new member in this position.</td></tr></table></div>';
            }

            return $table;
        }


        public function view_mydownlines()
        {

            global $wp_query, $current_user;
            $pageId = $wp_query->post->ID;
            $userID = $current_user->ID;
            echo "<h2>" . __('My Downlines', 'bmw') . "</h2>";
            $this->letscms_check_user();

            if (isset($_REQUEST['search_key']) &&  $_REQUEST['search_key'] != '') {
                $userKey = sanitize_text_field($_REQUEST['search_key']);
            } else {
                $userKey = $this->getKeyByUserId($userID);
            }

            $level = 3;
            if (isset($userKey)) {
                $data = $this->MyNetwork($userKey, $level);

                //echo'<pre>';print_r($data);
    ?>

             <script type='text/javascript' src='https://www.google.com/jsapi'></script>
             <script type='text/javascript'>
                 google.load('visualization', '1', {
                     packages: ['orgchart']
                 });
                 google.setOnLoadCallback(drawChart);

                 function drawChart() {
                     var data = new google.visualization.DataTable();
                     data.addColumn('string', 'Name');
                     data.addColumn('string', 'Manager');
                     data.addColumn({
                         'type': 'string',
                         'role': 'tooltip',
                         'p': {
                             'html': true
                         }
                     });
                     data.addRows([
                         <?php
                            $i = 1;

                            foreach ($data as $lists => $rowArr) {
                                if ($rowArr) {
                                    foreach ($rowArr as $rows => $row) {
                                        foreach ($row as $rowDetail) {
                                            $username = $rowDetail['username'];
                                            $position = $rowDetail['leg'];
                                            $parentKey = $rowDetail['parentKey'];
                                            $sponsorKey = $rowDetail['sponsorKey'];
                                            $payment_status = $rowDetail['payment_status'];

                                            if (empty($rowDetail['userKey'])) {
                                                if ($rowDetail['leg'] == 0) {
                                                    $userKey = $rowDetail['parentKey'] . '_addl';
                                                } else {
                                                    $userKey = $rowDetail['parentKey'] . '_addr';
                                                }
                                            } else {
                                                $userKey = $rowDetail['userKey'];
                                            }
                                            $detail = $this->user_detail($userKey, $username, $position, $sponsorKey, $parentKey, $payment_status);

                                            if (!empty($username)) {
                            ?>[{
                                                     v: '<?php echo $userKey; ?>',

                                                     f: '<div class="bmw_tooltip"><span><a href="<?php echo  '?page_id=' . $pageId . '&search_key=' . $rowDetail['userKey'] ?>"><?php echo ($payment_status == 'paid') ? '<img src="' . BMW_URL . '/assets/images/tree.png' . '" width="60px" >' : '<img src="' . BMW_URL . '/assets/images/unpaid.png' . '" width="60px" >'  ?></a></span><span class="bmw_tooltiptext"><?php echo $detail; ?></span></div>'
                                                 },
                                                 '<?php echo $rowDetail['parentKey']; ?>', ''],

                                         <?php
                                            } else {
                                            ?>[{
                                                     v: '<?php echo $userKey; ?>',

                                                     f: '<span class="name"><?php echo $rowDetail['name']; ?></span><br><span class="userkey"><?php echo $rowDetail['userKey'] ?></span>'
                                                 },
                                                 '<?php echo $rowDetail['parentKey']; ?>', ''],
                         <?php
                                            }
                                        }
                                    }
                                }
                            }
                            ?>
                     ]);
                     var options = {
                         tooltip: {
                             isHtml: false
                         },
                         legend: 'none',
                         allowHtml: true
                     };
                     var chart = new google.visualization.OrgChart(document.getElementById('mydownlines'));
                     chart.draw(data, options);

                 }

                 function searchUser() {
                     var user = document.getElementById("search_key").value;
                     if (user == "") {
                         alert('<?php echo __('Please enter the user key', 'bmw'); ?>');
                         document.getElementById("search_key").focus();
                         return false;
                     }
                 }
             </script>
             <style type="text/css">
                 .tbl-tooltip {
                     background-color: white;
                 }
             </style>

             <hr>
             <div class="container">
                 <?php $this->letscms_check_user(); ?>
                 <div class="row">
                     <div class="col-sm-12">
                         <form action="<?php echo bloginfo('url'); ?>/?page_id=<?php echo $pageId ?>" method="post">
                             <center><button class="btn btn-primary" id="" type="submit">My Downlines</button></center>
                         </form>
                     </div>
                 </div>
                 <div id="clear"></div>
                 <div class="row">
                     <div class="col-sm-12">
                         <form name="usersearch" id="usersearch" action="" method="post" onSubmit="return searchUser();">
                             <table>
                                 <tr>
                                     <td>Search By User Key:</td>
                                     <td><input type="text" class="form-control" name="search_key" id="search_key" placeholder="Enter User Key"></td>
                                     <td align="right"><button class="btn btn-primary" id="" type="submit" name="search">Search</button></td>
                                 </tr>
                             </table>
                         </form>
                     </div>
                 </div>
             </div>
             <style type="text/css">
                 table {
                     background-color: white;
                 }
             </style>
             <?php
                /*********************************Show Binary Network********************************************/
                $total_users = count($data, true);
                if ($total_users != '') { ?>
                 <div id='mydownlines'></div>

             <?php } else { ?>
                 <div class="usernotfound"> <?php echo __('Sorry ! No user found', 'bmw'); ?>. </div>
             <?php } ?>
 <?php }
        }
    }
