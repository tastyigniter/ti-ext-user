<?php if (count($customerAddresses)) { ?>
    <div class="list-group">
        <?php $index = 0;
        foreach ($customerAddresses as $address) { ?>
            <?php $index++; ?>
            <div
                class="list-group-item <?= ($customer->address_id == $address->address_id) ? 'list-group-item-info' : ''; ?>"
            >
                <address class="text-left"><?= format_address($address); ?></address>
                <span class="">
                    <a
                        class="edit-address"
                        href="<?= site_url('account/address', ['addressId' => $address->address_id]); ?>"
                    ><?= lang('sampoyigi.account::default.account.text_edit'); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;
                </span>
            </div>
        <?php } ?>
    </div>

    <div class="pagination-bar text-right">
        <div class="links"><?= $customerAddresses->links(); ?></div>
    </div>
<?php } else { ?>
    <div class="list-group-item"><?= lang('sampoyigi.account::default.account.text_no_address'); ?></div>
<?php } ?>

<div class="buttons">
    <a
        class="btn btn-primary btn-lg"
        data-request="<?= $addAddressEventHandler; ?>"
    ><?= lang('sampoyigi.account::default.account.button_add'); ?></a>
</div>
