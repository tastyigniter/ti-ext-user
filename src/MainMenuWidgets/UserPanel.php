<?php

namespace Igniter\User\MainMenuWidgets;

use Igniter\Admin\Traits\ValidatesForm;
use Igniter\Flame\Exception\FlashException;
use Igniter\Flame\Html\HtmlFacade;
use Igniter\User\Classes\UserState;
use Igniter\User\Models\User;
use Igniter\User\Subscribers\NavigationExtendUserMenuLinksEvent;

class UserPanel extends \Igniter\Admin\Classes\BaseMainMenuWidget
{
    use ValidatesForm;

    public array $links = [];

    protected User $user;

    protected UserState $userState;

    public function initialize()
    {
        $this->fillFromConfig([
            'links',
        ]);

        $this->user = $this->getController()->getUser();
        $this->userState = UserState::forUser($this->user);
    }

    public function render()
    {
        $this->prepareVars();

        return $this->makePartial('userpanel/userpanel');
    }

    public function prepareVars()
    {
        $this->vars['avatarUrl'] = $this->user->avatar_url;
        $this->vars['userName'] = $this->user->name;
        $this->vars['roleName'] = $this->user->role?->name;
        $this->vars['userIsOnline'] = $this->userState->isOnline();
        $this->vars['userIsIdle'] = $this->userState->isIdle();
        $this->vars['userIsAway'] = $this->userState->isAway();
        $this->vars['userStatusName'] = $this->userState->getStatusName();
        $this->vars['links'] = $this->listMenuLinks();
    }

    public function onLoadStatusForm()
    {
        $this->prepareVars();

        $this->vars['statuses'] = UserState::getStatusDropdownOptions();
        $this->vars['clearAfterOptions'] = UserState::getClearAfterMinutesDropdownOptions();
        $this->vars['message'] = $this->userState->getMessage();
        $this->vars['userStatus'] = $this->userState->getStatus();
        $this->vars['clearAfterMinutes'] = $this->userState->getClearAfterMinutes();
        $this->vars['statusUpdatedAt'] = $this->userState->getUpdatedAt();

        return $this->makePartial('userpanel/statusform');
    }

    public function onSetStatus()
    {
        $validated = $this->validate(request()->post(), [
            'status' => 'required|integer',
            'message' => 'nullable|string|max:128',
            'clear_after' => 'required_if:status,'.UserState::CUSTOM_STATUS.'|integer',
        ]);

        throw_if($validated['status'] < 1 && !strlen($validated['message']),
            new FlashException(lang('igniter::admin.side_menu.alert_invalid_status')),
        );

        $this->userState->updateState($validated['status'], $validated['message'] ?? '', $validated['clear_after']);

        return [
            '~#'.$this->getId() => $this->render(),
        ];
    }

    protected function listMenuLinks()
    {
        $items = collect($this->links);

        NavigationExtendUserMenuLinksEvent::dispatch($items);

        return $items
            ->mapWithKeys(function($item, $code) {
                $item = array_merge([
                    'priority' => 999,
                    'label' => null,
                    'cssClass' => null,
                    'iconCssClass' => null,
                    'attributes' => [],
                    'permission' => null,
                ], $item);

                if (array_key_exists('url', $item)) {
                    $item['attributes']['href'] = $item['url'];
                }

                $item['attributes'] = HtmlFacade::attributes($item['attributes']);

                return [
                    $code => (object)$item,
                ];
            })
            ->filter(function($item) {
                return !($permission = array_get($item, 'permission')) || $this->user->hasPermission($permission);
            })
            ->sortBy('priority');
    }
}
