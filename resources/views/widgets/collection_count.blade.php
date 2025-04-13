@php use function Statamic\trans as __; @endphp

@if ($errors->count())

    <div class="card p-4 text-sm text-red-500 overflow-hidden h-full group">
        @foreach ($errors as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>

@endif

@if ($collections->count() === 1)

    @php
        $collection = $collections->first();
    @endphp

    <div class="card p-0 overflow-hidden h-full group">
        @if ($collection->url)
            <a href="{{ $collection->url }}">
        @endif
            <div class="p-4 pb-0 text-sm">
                <p class="truncate">
                    <span>{{ __($collection->title) }}</span>
                    <span class="opacity-0 group-hover:opacity-100">→</span>
                </p>
            </div>
            <div class="p-4 pb-2 pt-0 text-4xl">
                <p>
                    <span>{{ $collection->count }}</span>
                </p>
            </div>
        @if ($collection->url)
            </a>
        @endif
    </div>

@elseif ($collections->count() > 1)

    <div class="@container flex flex-wrap -mx-4">
        @foreach ($collections as $collection)
            <div class="w-full @md:w-1/2 @lg:w-1/4 group">
                @if ($collection->url)
                    <a href="{{ $collection->url }}">
                @endif
                    <div class="px-4 text-sm">
                        <p class="truncate">
                            <span>{{ __($collection->title) }}</span>
                            <span class="opacity-0 group-hover:opacity-100">→</span>
                        </p>
                    </div>
                    <div class="px-4 text-4xl">
                        <p>
                            <span>{{ $collection->count }}</span>
                        </p>
                    </div>
                @if ($collection->url)
                    </a>
                @endif
            </div>
        @endforeach
    </div>

@endif
