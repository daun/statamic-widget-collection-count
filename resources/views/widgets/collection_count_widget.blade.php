@php use function Statamic\trans as __; @endphp

@foreach ($errors as $error)
    <ui-badge color="red" icon="warning-diamond">{{ $error }}</ui-badge>
@endforeach

@if ($grid && $cards)
    <div class="@container grid grid-cols-[repeat(auto-fill,minmax(clamp(4em,100%/9,6em),1fr))] gap-x-6 gap-y-4 text-5xl">
@elseif ($grid)
    <div class="@container grid grid-cols-[repeat(auto-fill,minmax(clamp(3.5em,100%/10,5.5em),1fr))] gap-4 pt-2! pb-3! text-5xl">
@endif

    @foreach ($collections as $collection)
        @include('daun::widgets.collection_count_item', ['collection' => $collection, 'card' => $cards])
    @endforeach

@if ($grid)
    </div>
@endif
