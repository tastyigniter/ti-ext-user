<?php if (count($customerMessages)) { ?>
    <div
        class="list-group"
        id="account-inbox"
        role="tablist"
        aria-multiselectable="true"
    >
        <?php $index = 0;
        foreach ($customerMessages as $message) {
            $index++; ?>
            <div
                id="heading-<?= $index ?>"
                class="list-group-item <?= $message->state; ?>"
                role="tab"
            >
                <h4>
                    <a
                        role="button"
                        data-toggle="collapse"
                        data-parent="#account-inbox"
                        href="#inbox-message-<?= $index ?>"
                        aria-expanded="true"
                        aria-controls="inbox-message-<?= $index ?>"
                    >
                        <b><?= $message->subject; ?></b>
                        <span class="badge pull-right"><?= day_elapsed($message->date_added); ?></span>
                    </a>
                </h4>
                <div id="inbox-message-<?= $index ?>"
                     class="collapse"
                     role="tabpanel"
                     aria-labelledby="heading-<?= $index ?>"
                ><?= $message->body; ?></div>
            </div>
        <?php } ?>
    </div>

    <div class="pagination-bar text-right">
        <div class="links"><?= $customerMessages->links(); ?></div>
    </div>
<?php } else { ?>
    <div class="card-body">
        <p><?= lang('igniter.user::default.inbox.text_empty'); ?></p>
    </div>
<?php } ?>