<div id="address-book">
        @if ($addressIdParam)
                @partial('@form')
        @else
                @partial('@list')
        @endif
</div>
