<form
    role="form"
    method="POST"
    accept-charset="utf-8"
    data-request="<?= $__SELF__.'::onLeaveReview'; ?>"
>
    <input
        type="hidden"
        name="location_id"
        value="<?= $reviewSale->location_id; ?>"
        <?= set_value('location_id', $reviewSale->location_id); ?>
    />
    <input
        type="hidden"
        name="customer_id"
        value="<?= $reviewSale->customer_id; ?>"
    />
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
        <div class="form-inline">
            <div class="form-group wrap-horizontal wrap-right">
                <label for="quality"><?= lang('igniter.user::default.reviews.label_quality'); ?></label>
                <div
                    class="rating rating-star"
                    data-score="<?= $reviewSale->quality; ?>"
                    data-score-name="rating[quality]"
                ></div>
                <?= form_error('rating.quality', '<span class="text-danger">', '</span>'); ?>
            </div>
            <div class="form-group wrap-horizontal wrap-right">
                <label for="delivery"><?= lang('igniter.user::default.reviews.label_delivery'); ?></label>
                <div
                    class="rating rating-star"
                    data-score="<?= $reviewSale->delivery; ?>"
                    data-score-name="rating[delivery]"
                ></div>
                <?= form_error('rating.delivery', '<span class="text-danger">', '</span>'); ?>
            </div>
            <div class="form-group wrap-horizontal">
                <label for="service"><?= lang('igniter.user::default.reviews.label_service'); ?></label>
                <div
                    class="rating rating-star"
                    data-score="<?= $reviewSale->service; ?>"
                    data-score-name="rating[service]"
                ></div>
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
            ><?= set_value('review_text'); ?></textarea>
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
