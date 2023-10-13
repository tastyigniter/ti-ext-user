<li
  id="{{ $this->getId() }}"
  class="nav-item dropdown"
>
    <a
        href="#"
        class="nav-link"
        data-bs-toggle="dropdown"
        data-bs-auto-close="outside"
        data-bs-display="static"
    >
        <i class="fa fa-bell" role="button"></i>
        <span @class([
      'badge text-bg-danger',
      'hide' => !$unreadCount,
    ])>&nbsp;</span>
  </a>
  <ul class="dropdown-menu dropdown-menu-end">
    <li class="dropdown-header">
      <div class="d-flex justify-content-between">
        <div class="flex-fill">@lang('igniter.user::default.notifications.text_title')</div>
        @if($notifications->isNotEmpty())
          <div>
            <a
              class="cursor-pointer"
              data-request="{{$this->getEventHandler('onMarkAsRead')}}"
              title="@lang('igniter.user::default.notifications.button_mark_as_read')"
            ><i class="fa fa-check"></i></a>
          </div>
        @endif
      </div>
    </li>
    <ul class="menu">
      @forelse($notifications as $notification)
        <li class="menu-item{{ !$notification->read_at ? ' active' : '' }}">
          <a href="{{ $notification->url }}" class="menu-link">
            {!! $this->makePartial('notifications.notification', ['notification' => $notification]) !!}
          </a>
        </li>
        <li class="divider"></li>
      @empty
        <li class="p-3 text-center">@lang('igniter.user::default.notifications.text_empty')</li>
      @endforelse
    </ul>
    <li class="dropdown-footer">
      <a class="text-center" href="{{ admin_url('notifications') }}"><i class="fa fa-ellipsis-h"></i></a>
    </li>
  </ul>
</li>
