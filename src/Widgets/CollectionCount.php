<?php

namespace Daun\CollectionCount\Widgets;

use Illuminate\Support\Collection as LaravelCollection;
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
        $collections = $this->getCollectionsFromConfig();
        [$collections, $errors] = $this->augmentCollections($collections);

        return view('daun::widgets.collection_count', [
            'collections' => $collections,
            'errors' => $errors
        ]);
    }

    protected function getCollectionsFromConfig(): LaravelCollection
    {
        $collections = $this->config('collections', $this->config('collection', []));

        if ($collections === '*') {
            $collections = Collection::handles();
        }

        if (is_string($collections)) {
            $collections = collect(explode('|', $collections));
        }

        return collect($collections);
    }

    protected function augmentCollections(LaravelCollection $collections): array
    {
        $errors = $collections->count()
            ? $collections
                ->filter(fn($handle) => !Collection::handleExists($handle))
                ->map(fn($handle) => "Error: Collection [$handle] doesn't exist.")
            : collect('Error: No collections specified');

        $augmented = $collections
            ->filter(fn($handle) => Collection::handleExists($handle))
            ->map(fn($handle) => $this->augmentCollection($handle));

        return [$augmented, $errors];
    }

    protected function augmentCollection(mixed $handle): ?object
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
