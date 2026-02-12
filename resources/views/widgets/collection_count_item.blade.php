@if ($card)
    <ui-card inset>
        <div class="relative px-4! sm:px-4.5! py-5!">
            @include('daun::widgets.collection_count_info', ['collection' => $collection])
        </div>
    </ui-card>
@else
    <div class="relative">
        @include('daun::widgets.collection_count_info', ['collection' => $collection])
    </div>
@endif
