@php use function Statamic\trans as __; @endphp

@foreach ($errors as $error)
    <ui-badge color="red" icon="warning-diamond">{{ $error }}</ui-badge>
@endforeach

@if ($collections->count())

    <div class="@container flex flex-wrap gap-x-6 gap-y-4">
        @foreach ($collections as $collection)
            <div class="group relative hover:opacity-60">
                <p class="truncate flex gap-1 items-baseline">
                    <span>{{ __($collection->title) }}</span>
                    @if ($collection->url)
                        <span class="opacity-0 group-hover:opacity-100">→</span>
                    @endif
                </p>
                <div class="text-6xl lining-nums">
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
