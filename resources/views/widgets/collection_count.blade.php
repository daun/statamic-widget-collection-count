@php use function Statamic\trans as __; @endphp

@foreach ($errors as $error)
    <ui-badge color="red" icon="warning-diamond">{{ $error }}</ui-badge>
@endforeach

@if ($collections->count())

    @php
        $maxCount = $collections->max(fn($collection) => strlen("{$collection->count}"));
    @endphp

    <div class="@container grid grid-cols-[repeat(auto-fill,minmax(clamp(3.5em,100%/9,5em),1fr))] gap-x-6 gap-y-4 pb-3 text-5xl" data-max-count="{{ $maxCount }}">
        @foreach ($collections as $collection)
            <ui-card>
                <div class="group relative -mt-1.5! -mb-1! hover:opacity-70">
                    <p class="text-base truncate flex gap-2 items-baseline">
                        <span>{{ __($collection->title) }}</span>
                        @if ($collection->url)
                            <span class="opacity-0 group-hover:opacity-100">→</span>
                        @endif
                    </p>
                    <div class="mt-1! -ml-0.5! lining-nums font-medium">
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
            </ui-card>
        @endforeach
    </div>

@endif
