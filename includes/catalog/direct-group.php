<?php
class BMW_Direct_group
{
    use Letscms_BMW_CommonClass;

    public function view_directgroup()
    {
        $key = $this->get_current_user_key();

        $data     = $this->MyDirectGroupDetails($key);
        $totalArr     = $this->MyDirectGroupTotal($key);

?>
        <div class="container">
            <h2><?php echo __('Direct Group Details', 'bmw'); ?></h2>
            <?php $this->letscms_check_user(); ?>
            <div class="row">
                <div class="col-sm-4"><?php echo __('Total downlines : ', 'bmw'); ?><?php echo $totalArr['total']; ?></div>
                <div class="col-sm-4"><?php echo __('Left downlines : ', 'bmw'); ?><?php echo $totalArr['left']; ?></div>
                <div class="col-sm-4"><?php echo __('Right downlines : ', 'bmw'); ?><?php echo $totalArr['right']; ?></div>
            </div>
            <div id="clear"></div>
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col"><?php echo __('S No.', 'bmw'); ?></th>
                                <th scope="col"><?php echo __('My Downlines', 'bmw'); ?></th>
                                <th scope="col"><?php echo __('User Key', 'bmw'); ?></th>
                                <th scope="col"><?php echo __('Parent Key', 'bmw'); ?></th>
                                <th scope="col"><?php echo __('Placement', 'bmw'); ?></th>
                                <th scope="col"><?php echo __('Joining Date', 'bmw'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($data as $consultant) {
                            ?>
                                <tr>
                                    <td><?php echo $consultant['id']; ?></td>
                                    <td><?php echo $consultant['name']; ?></td>
                                    <td><?php echo $consultant['userKey']; ?></td>
                                    <td><?php echo $consultant['parentKey']; ?></td>
                                    <td><?php echo $consultant['leg']; ?></td>
                                    <td><?php echo $consultant['creationDate']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
<?php
    }
}
