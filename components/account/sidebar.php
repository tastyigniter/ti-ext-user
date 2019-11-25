<div class="nav flex-column">
    <a
        href="<?= site_url($accountPage); ?>"
        class="nav-item nav-link <?= ($this->page->getId() == 'account-account') ? 'active' : ''; ?>"
    >
        <span class="fa fa-user"></span>&nbsp;&nbsp;&nbsp;
        <?= lang('igniter.user::default.text_account'); ?>
    </a>
    <a
        href="<?= site_url($detailsPage); ?>"
        class="nav-item nav-link <?= ($this->page->getId() == 'account-details') ? 'active' : ''; ?>"
    >
        <span class="fa fa-edit"></span>&nbsp;&nbsp;&nbsp;
        <?= lang('igniter.user::default.text_edit_details'); ?>
    </a>
    <a
        href="<?= site_url($addressPage); ?>"
        class="nav-item nav-link <?= ($this->page->getId() == 'account-address') ? 'active' : ''; ?>"
    >
        <span class="fa fa-book"></span>&nbsp;&nbsp;&nbsp;
        <?= lang('igniter.user::default.text_address'); ?>
    </a>
    <a
        href="<?= site_url($ordersPage); ?>"
        class="nav-item nav-link <?= ($this->page->getId() == 'account-orders') ? 'active' : ''; ?>"
    >
        <span class="fa fa-list-alt"></span>&nbsp;&nbsp;&nbsp;
        <?= lang('igniter.user::default.text_orders'); ?>
    </a>

    <a
        href="<?= site_url($reservationsPage); ?>"
        class="nav-item nav-link <?= ($this->page->getId() == 'account-reservations') ? 'active' : ''; ?>"
    >
        <span class="fa fa-calendar"></span>&nbsp;&nbsp;&nbsp;
        <?= lang('igniter.user::default.text_reservations'); ?>
    </a>

</div>
