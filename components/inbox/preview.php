<div class="row">
    <div class="table-responsive">
        <table class="table table-none">
            <tr>
                <td width="20%"><b><?php echo lang('column_date'); ?>:</b></td>
                <td><?php echo $date_added; ?></td>
            </tr>
            <tr>
                <td><b><?php echo lang('column_subject'); ?>:</b></td>
                <td><?php echo $subject; ?></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="msg_body"><?php echo $body; ?></div>
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="row wrap-all">
    <div class="buttons">
        <a class="btn btn-default" href="<?php echo $back_url; ?>"><?php echo lang('button_back'); ?></a>
        <a class="btn btn-danger"
           href="<?php echo $delete_url; ?>"><?php echo lang('button_delete'); ?></a>
    </div>
</div>
