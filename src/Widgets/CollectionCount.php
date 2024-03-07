<?php

namespace Daun\CollectionCount\Widgets;

use Statamic\Facades\Collection;
use Statamic\Facades\User;
use Statamic\Widgets\Widget;

class CollectionCount extends Widget
{
    /**
     * The HTML that should be shown in the widget.
     *
     * @return string|\Illuminate\View\View
     */
    public function html()
    {
        $collections = $this->config('collections', $this->config('collection', []));
        if ($collections === '*') {
            $collections = Collection::handles();
        }
        if (is_string($collections)) {
            $collections = collect(explode('|', $collections));
        }
        $collections = collect($collections);
        if (!$collections->count()) {
            return view('daun::widgets.collection_count', [
                'errors' => collect('Error: No collections specified')
            ]);
        }

        $errors = $collections
            ->filter(fn($handle) => !Collection::handleExists($handle))
            ->map(fn($handle) => "Error: Collection [$handle] doesn't exist.");

        if ($errors->count()) {
            return view('daun::widgets.collection_count', [
                'errors' => $errors
            ]);
        }

        $collections = $collections->map(fn($handle) => $this->augmentCollection($handle));

        return view('daun::widgets.collection_count', [
            'collections' => $collections
        ]);
    }

    protected function augmentCollection($handle)
    {
        $collection = Collection::findByHandle($handle);
        if (!$collection) {
            return null;
        }

        $url = User::current()->can('view', $handle) ? $collection->showUrl() : null;
        $count = $collection->queryEntries($collection)->count(); // ->where('published', true)

        return (object) [
            'title' => $collection->title(),
            'handle' => $collection->handle(),
            'url' => $url,
            'count' => $count
        ];
    }
}
