@php use function Statamic\trans as __; @endphp

@foreach ($errors as $error)
    <ui-badge color="red" icon="warning-diamond">{{ $error }}</ui-badge>
@endforeach

@if ($collections->count())

    @php
        $maxCount = $collections->max(fn($collection) => strlen("{$collection->count}"));
    @endphp

    <div class="@container grid grid-cols-[repeat(auto-fill,minmax(var(--collection-count-col,1.5em),1fr))] gap-x-6 gap-y-4 text-6xl" data-max-count="{{ $maxCount }}">
        @foreach ($collections as $collection)
            <div class="group relative hover:opacity-60">
                <p class="text-base truncate flex gap-1.5 items-baseline">
                    <span>{{ __($collection->title) }}</span>
                    @if ($collection->url)
                        <span class="opacity-0 group-hover:opacity-100">→</span>
                    @endif
                </p>
                <div class="lining-nums -mx-0.5">
                    <p>
                        <span>{{ $collection->count }}</span>
                    </p>
                </div>
                @if ($collection->url)
                    <a href="{{ $collection->url }}" class="absolute opacity-0 inset-0 select-none">
                        {{ __($collection->title) }}
                    </a>
                @endif
            </div>
        @endforeach
    </div>

@endif
