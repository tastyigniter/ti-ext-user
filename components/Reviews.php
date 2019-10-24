<?php namespace Igniter\User\Components;

use Admin\Models\Orders_model;
use Admin\Models\Reservations_model;
use Admin\Models\Reviews_model;
use Admin\Traits\ValidatesForm;
use Auth;
use Exception;
use Igniter\Flame\Exception\ApplicationException;
use Main\Traits\HasPageOptions;
use Redirect;
use Request;

class Reviews extends \System\Classes\BaseComponent
{
    use ValidatesForm;
    use HasPageOptions;

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'label' => 'Page Number',
                'type' => 'string',
            ],
            'itemsPerPage' => [
                'label' => 'Items Per Page',
                'type' => 'number',
                'default' => 20,
            ],
            'sortOrder' => [
                'label' => 'Sort order',
                'type' => 'string',
            ],
            'redirectPage' => [
                'label' => 'Page to redirect to when reviews is disabled',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'account/account',
            ],
            'reviewsPage' => [
                'label' => 'Page to redirect to when reviews was successfully added',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'account/reviews',
            ],
            'ordersRedirectPage' => [
                'label' => 'Orders Page',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'account/orders',
            ],
            'reservationsRedirectPage' => [
                'label' => 'Reservations Page',
                'type' => 'select',
                'options' => [static::class, 'getPageOptions'],
                'default' => 'account/reservations',
            ],
        ];
    }

    public function onRun()
    {
        $this->page['showReviews'] = $showReviews = setting('allow_reviews') == 1;
        $this->page['customerReviews'] = $this->loadReviews();
        $this->page['customerReview'] = $this->getReview();
        $this->page['reviewHints'] = $this->getHints();

        $this->page['saleIdParam'] = $saleIdParam = $this->param('saleId');
        $this->page['saleTypeParam'] = $this->param('saleType');
        $this->page['reviewSale'] = $model = $this->getSaleModel();

        if (!$showReviews) {
            flash()->error(lang('igniter.user::default.reviews.alert_review_disabled'))->now();

            return Redirect::to($this->controller->pageUrl($this->property('redirectPage')));
        }

        if ($saleIdParam AND !$model) {
            flash()->warning(lang('igniter.user::default.reviews.alert_review_status_history'))->now();

            return Redirect::to($this->makeRedirectUrl());
        }
    }

    public function onLeaveReview()
    {
        try {
            $customer = Auth::customer();
            $reviewable = $this->getSaleModel();

            if (Reviews_model::checkReviewed($reviewable, $customer))
                throw new ApplicationException(lang('igniter.user::default.reviews.alert_review_duplicate'));

            $data = post();

            $rules = [
                ['rating.quality', 'lang:igniter.user::default.reviews.label_quality', 'required|integer'],
                ['rating.delivery', 'lang:igniter.user::default.reviews.label_delivery', 'required|integer'],
                ['rating.service', 'lang:igniter.user::default.reviews.label_service', 'required|integer'],
                ['review_text', 'lang:igniter.user::default.reviews.label_review', 'required|min:2|max:1028'],
            ];

            $this->validate($data, $rules);

            $model = new Reviews_model();
            $model->location_id = $reviewable->location_id;
            $model->customer_id = $customer->customer_id;
            $model->author = $customer->full_name;
            $model->sale_id = $reviewable->getKey();
            $model->sale_type = $reviewable->getMorphClass();
            $model->quality = array_get($data, 'rating.quality');
            $model->delivery = array_get($data, 'rating.delivery');
            $model->service = array_get($data, 'rating.service');
            $model->review_text = array_get($data, 'review_text');
            $model->review_status = (setting('approve_reviews') === 1) ? 1 : 0;

            $model->save();

            flash()->success(lang('igniter.user::default.reviews.alert_review_success'))->now();

            return Redirect::to($this->controller->pageUrl($this->property('reviewsPage'), [
                'saleType' => null,
                'saleId' => null,
            ]));
        }
        catch (Exception $ex) {
            if (Request::ajax()) throw $ex;
            else flash()->alert($ex->getMessage());
        }
    }

    /**
     * @return mixed
     */
    protected function getHints()
    {
        return array_get(setting('ratings'), 'ratings', [
            1 => 'Worse', 2 => 'Bad', 3 => 'Good', 4 => 'Average', 5 => 'Excellent',
        ]);
    }

    protected function getReview()
    {
        $reviewId = (int)$this->param('saleType');

        return Reviews_model::where('customer_id', Auth::getId())->findOrNew($reviewId);
    }

    protected function loadReviews()
    {
        if (!$customer = Auth::customer())
            return [];

        return Reviews_model::with(['location'])->listFrontEnd([
            'page' => $this->param('page'),
            'pageLimit' => $this->property('itemsPerPage'),
            'sort' => $this->property('sortOrder', 'date_added desc'),
            'customer' => $customer,
        ]);
    }

    protected function getSaleModel()
    {
        if ($this->param('saleType') == 'reservation') {
            $query = Reservations_model::whereKey($this->param('saleId'));

            return $query->whereHas('status_history', function ($q) {
                $q->where('status_id', setting('confirmed_reservation_status'));
            })->first();
        }

        if ($this->param('saleType') == 'order') {
            $query = Orders_model::whereKey($this->param('saleId'));

            return $query->whereHas('status_history', function ($q) {
                $q->where('status_id', setting('completed_order_status'));
            })->first();
        }
    }

    protected function makeRedirectUrl()
    {
        return $this->controller->pageUrl($this->property($this->param('saleType').'sRedirectPage'), ['orderId' => '']);
    }
}