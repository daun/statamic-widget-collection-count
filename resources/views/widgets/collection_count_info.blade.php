@php use function Statamic\trans as __; @endphp

<div class="group -mt-1.5! -mb-1! text-5xl">
    <p class="text-base truncate flex gap-2 items-baseline group-hover:text-ui-accent-text">
        <span>{{ __($collection->title) }}</span>
        @if ($collection->url)
            <span class="opacity-0 group-hover:opacity-100 group-hover:text-ui-accent-text">→</span>
        @endif
    </p>
    <div class="mt-1! -ml-0.5! lining-nums">
        <p>
            <span class="group-hover:text-ui-accent-text">{{ $collection->count }}</span>
        </p>
    </div>
    @if ($collection->url)
        <a href="{{ $collection->url }}" class="absolute opacity-0 inset-0 select-none">
            {{ __($collection->title) }}
        </a>
    @endif
</div>
