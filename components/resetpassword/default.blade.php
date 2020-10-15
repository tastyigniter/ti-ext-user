@if ($__SELF__->resetCode())
    @partial('@reset')
@else
    @partial('@forgot')
@endif
