<?php
$customerOrders = $__SELF__->getCustomerOrders();
$customerReservations = $__SELF__->getCustomerReservations();
$customerMessages = $__SELF__->getCustomerMessages();
?>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h4><?= sprintf(lang('igniter.user::default.text_welcome'), $customer->first_name); ?></h4>
                <?php if (!empty($customer->address)) { ?>
                    <a
                        class="edit-address pull-right"
                        href="<?= site_url('account/address/'.$customer->address->getKey()); ?>"
                    ><?= lang('igniter.user::default.text_edit'); ?></a>
                    <b><?= lang('igniter.user::default.text_default_address'); ?></b>
                    <address class="text-left text-overflow"><?= format_address($customer->address); ?></address>
                <?php }
                else { ?>
                    <p><?= lang('igniter.user::default.text_no_default_address'); ?></p>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <i class="fa fa-shopping-basket fa-2x"></i>
                <?php if ($__SELF__->cartCount()) { ?>
                    <p><?= sprintf(lang('igniter.user::default.text_cart_summary'), $__SELF__->cartCount(), $__SELF__->cartTotal()); ?></p>
                    <a class="btn btn-primary" href="<?= site_url('checkout/checkout'); ?>">
                        <?= lang('igniter.user::default.text_checkout'); ?>
                    </a>
                <?php }
                else { ?>
                    <p><?= lang('igniter.user::default.text_no_cart_items'); ?></p>
                    <a class="btn btn-primary" href="<?= restaurant_url('local/menus'); ?>">
                        <?= lang('igniter.user::default.text_order'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-0"><?= lang('igniter.user::default.text_orders'); ?></h4>
            </div>
            <?php if (count($customerOrders)) { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-none">
                        <thead>
                        <tr>
                            <th><?= lang('igniter.user::default.column_id'); ?></th>
                            <th width="65%" class="text-center">
                                <?= lang('igniter.user::default.column_status'); ?>
                            </th>
                            <th><?= lang('igniter.user::default.column_date'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($customerOrders as $order) { ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('account/orders/'.$order->order_id); ?>">
                                        <?= $order->order_id; ?>
                                    </a>
                                </td>
                                <td width="65%" class="text-center"><?= $order->status_name; ?></td>
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
                <p class="card-body"><?= lang('igniter.user::default.text_no_orders'); ?></p>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-0"><?= lang('igniter.user::default.text_reservations'); ?></h4>
            </div>
            <?php if (count($customerReservations)) { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-none">
                        <thead>
                        <tr>
                            <th><?= lang('igniter.user::default.column_id'); ?></th>
                            <th width="65%" class="text-center">
                                <?= lang('igniter.user::default.column_status'); ?>
                            </th>
                            <th><?= lang('igniter.user::default.column_date'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($customerReservations as $reservation) { ?>
                            <tr>
                                <td>
                                    <a href="<?= site_url('account/reservations/'.$reservation->reservation_id); ?>">
                                        <?= $reservation->reservation_id; ?>
                                    </a>
                                </td>
                                <td width="65%" class="text-center"><?= $reservation->status_name; ?></td>
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
                <p class="card-body"><?= lang('igniter.user::default.text_no_reservations'); ?></p>
            <?php } ?>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-0"><?= lang('igniter.user::default.text_inbox'); ?></h4>
            </div>
            <?php if (count($customerMessages)) { ?>
                <div class="table-responsive">
                    <table class="table table-striped table-none">
                        <thead>
                        <tr>
                            <th><?= lang('igniter.user::default.column_date'); ?></th>
                            <th><?= lang('igniter.user::default.column_subject'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($customerMessages as $message) { ?>
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
                <p class="card-body"><?= lang('igniter.user::default.text_no_inbox'); ?></p>
            <?php } ?>
        </div>
    </div>
</div>
