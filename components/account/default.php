<div class="row">
    <div class="col-md-6">
        <h3><?= sprintf(lang('main::account.text_welcome'), $customer->first_name); ?></h3>
        <?php if (count($customer->address)) { ?>
            <p>
                <a
                    class="edit-address pull-right"
                    href="<?= site_url('account/address/'.$customer->address->getKey()); ?>"
                ><?= lang('main::account.text_edit'); ?></a>
                <b><?= lang('main::account.text_default_address'); ?></b>
            </p>
            <address class="text-left text-overflow"><?= format_address($customer->address); ?></address>
        <?php }
        else { ?>
            <p><?= lang('main::account.text_no_default_address'); ?></p>
        <?php } ?>
    </div>

    <div class="col-md-6">
        <div class="text-center">
            <i class="fa fa-shopping-basket fa-2x"></i>
            <?php if ($cartCount) { ?>
                <p><?= sprintf(lang('main::account.text_cart_summary'), $cartCount, $cartTotal); ?></p>
                <a class="btn btn-primary" href="<?= site_url('checkout/checkout'); ?>">
                    <?= lang('main::account.text_checkout'); ?>
                </a>
            <?php }
            else { ?>
                <p><?= lang('main::account.text_no_cart_items'); ?></p>
                <a class="btn btn-primary" href="<?= restaurant_url('local/menus'); ?>">
                    <?= lang('main::account.text_order'); ?>
                </a>
            <?php } ?>
        </div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-default" href="<?= site_url('account/details'); ?>">
            <?= lang('main::account.text_change_password'); ?>
        </a>
    </div>

    <div class="hide col-md-12">
        <h3><?= lang('main::account.text_orders'); ?></h3>
        <?php if (count($customer->orders)) { ?>
            <div class="table-responsive">
                <table class="table table-none">
                    <thead>
                    <tr>
                        <th><?= lang('main::account.column_id'); ?></th>
                        <th width="80%"
                            class="text-center"><?= lang('main::account.column_status'); ?></th>
                        <th><?= lang('main::account.column_date'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($customer->orders as $order) { ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('account/orders/'.$order->order_id); ?>">
                                    <?= $order->order_id; ?>
                                </a>
                            </td>
                            <td width="80%"
                                class="text-center"><?= $order->status_name; ?></td>
                            <td>
                                <?= $order->order_time; ?> - <?= day_elapsed($order->order_date); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php }
        else { ?>
            <p><?= lang('main::account.text_no_orders'); ?></p>
        <?php } ?>
    </div>

    <div class="hide col-md-12">
        <h3><?= lang('main::account.text_reservations'); ?></h3>
        <?php if (count($customer->reservations)) { ?>
            <div class="table-responsive">
                <table class="table table-none">
                    <thead>
                    <tr>
                        <th><?= lang('main::account.column_id'); ?></th>
                        <th><?= lang('main::account.column_status'); ?></th>
                        <th><?= lang('main::account.column_date'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($customer->reservations as $reservation) { ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('account/reservations/'.$reservation->reservation_id); ?>">
                                    <?= $reservation->reservation_id; ?>
                                </a>
                            </td>
                            <td><?= $reservation->status_name; ?></td>
                            <td>
                                <?= $reservation->reserve_time; ?> - <?= day_elapsed($reservation->reserve_date); ?>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php }
        else { ?>
            <p><?= lang('main::account.text_no_reservations'); ?></p>
        <?php } ?>
    </div>

    <div class="hide col-md-12">
        <h3><?= lang('main::account.text_inbox'); ?></h3>
        <?php if (count($customer->messages)) { ?>
            <div class="table-responsive">
                <table class="table table-none">
                    <thead>
                    <tr>
                        <th><?= lang('main::account.column_date'); ?></th>
                        <th><?= lang('main::account.column_subject'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($customer->messages as $message) { ?>
                        <tr class="<?= ($message->state == '0') ? 'unread' : 'read'; ?>">
                            <td>
                                <a class="edit"
                                   href="<?= site_url('account/inbox/'.$message->message_id); ?>"
                                >
                                    <?= $message->subject; ?>
                                </a><br/>
                                <small><?= substr(strip_tags(html_entity_decode($message->body, ENT_QUOTES, 'UTF-8')), 0, 50).'..'; ?></small>
                            </td>
                            <td><?= day_elapsed($message->date_added); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php }
        else { ?>
            <p><?= lang('main::account.text_no_inbox'); ?></p>
        <?php } ?>
    </div>
</div>
