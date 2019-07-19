<form
    role="form"
    method="POST"
    accept-charset="utf-8"
    data-request="<?= $__SELF__.'::onLeaveReview'; ?>"
>
    <div class="col-md-12">
        <div class="form-group">
            <label for="location"><?= lang('igniter.user::default.reviews.label_restaurant'); ?></label>
            <input
                type="text"
                id="location"
                class="form-control"
                value="<?= $reviewSale->location->location_name; ?>"
                disabled
            />
        </div>
        <div class="form-group">
            <label for="customer"><?= lang('igniter.user::default.reviews.label_customer_name'); ?></label>
            <input
                type="text"
                id="customer"
                class="form-control"
                value="<?= $reviewSale->customer_name; ?>"
                disabled
            />
        </div>
        <div class="d-flex justify-content-center">
            <div class="form-group flex-fill">
                <label class="control-label" for="quality"><?= lang('igniter.user::default.reviews.label_quality'); ?></label>
                <?php foreach ($reviewHints as $key => $hint) { ?>
                    <label class="custom-control custom-radio">
                        <input
                            type="radio"
                            name="rating[quality]"
                            id="ratingQuality<?= $key; ?>"
                            value="<?= $key; ?>"
                            class="custom-control-input"
                            <?= $key == $customerReview->quality ? 'checked="checked"' : '' ?>
                        ><label class="custom-control-label" for="ratingQuality<?= $key; ?>"><?= $hint; ?></label>
                    </label>
                <?php } ?>
                <?= form_error('rating.quality', '<span class="text-danger">', '</span>'); ?>
            </div>
            <div class="form-group flex-fill">
                <label class="control-label" for="delivery"><?= lang('igniter.user::default.reviews.label_delivery'); ?></label>
                <?php foreach ($reviewHints as $key => $hint) { ?>
                    <label class="custom-control custom-radio">
                        <input
                            type="radio"
                            name="rating[delivery]"
                            id="ratingDelivery<?= $key; ?>"
                            value="<?= $key; ?>"
                            class="custom-control-input"
                            <?= $key == $customerReview->delivery ? 'checked="checked"' : '' ?>
                        ><label class="custom-control-label" for="ratingDelivery<?= $key; ?>"><?= $hint; ?></label>
                    </label>
                <?php } ?>
                <?= form_error('rating.delivery', '<span class="text-danger">', '</span>'); ?>
            </div>
            <div class="form-group flex-fill">
                <label class="control-label" for="service"><?= lang('igniter.user::default.reviews.label_service'); ?></label>
                <?php foreach ($reviewHints as $key => $hint) { ?>
                    <label class="custom-control custom-radio">
                        <input
                            type="radio"
                            name="rating[service]"
                            id="ratingService<?= $key; ?>"
                            value="<?= $key; ?>"
                            class="custom-control-input"
                            <?= $key == $customerReview->service ? 'checked="checked"' : '' ?>
                        ><label class="custom-control-label" for="ratingService<?= $key; ?>"><?= $hint; ?></label>
                    </label>
                <?php } ?>
                <?= form_error('rating.service', '<span class="text-danger">', '</span>'); ?>
            </div>
        </div>
        <div class="form-group">
            <label for="review-text"><?= lang('igniter.user::default.reviews.label_review'); ?></label>
            <textarea
                name="review_text"
                id="review-text"
                rows="5"
                class="form-control"
            ><?= set_value('review_text', $customerReview->review_text); ?></textarea>
            <?= form_error('review_text', '<span class="text-danger">', '</span>'); ?>
        </div>
    </div>

    <div class="col-md-12">
        <div class="buttons">
            <button
                type="submit"
                class="btn btn-success"
            ><?= lang('igniter.user::default.reviews.button_review'); ?></button>
        </div>
    </div>
</form>
