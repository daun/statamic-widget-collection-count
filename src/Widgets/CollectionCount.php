<?php

namespace Daun\CollectionCount\Widgets;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection as LaravelCollection;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as Collections;
use Statamic\Facades\Scope;
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
        [$collections, $errors] = $this->queryCollections(
            collect(Arr::wrap($this->config('collection')))
        );

        return view('daun::widgets.collection_count', [
            'collections' => $collections,
            'errors' => $errors
        ]);
    }

    protected function queryCollections(LaravelCollection $collections): array
    {
        $errors = $collections->filter()->count()
            ? $collections
                ->filter(fn($handle) => !Collections::handleExists($handle))
                ->map(fn($handle) => "Error: Collection [$handle] doesn't exist.")
            : collect('Error: No collections specified');

        $result = $collections
            ->map(fn($handle) => Collections::findByHandle($handle))
            ->filter()
            ->map(fn($collection) => $this->queryCollection($collection));

        return [$result, $errors];
    }

    protected function queryCollection(Collection $collection): ?object
    {
        $query = $collection->queryEntries($collection);
        $count = $this->applyQueryScopes($query)->count();
        $url = $this->getViewUrl($collection);

        return (object) [
            'title' => $collection->title(),
            'handle' => $collection->handle(),
            'url' => $url,
            'count' => $count
        ];
    }

    protected function applyQueryScopes($query)
    {
        $limitToPublished = ! $this->config('count_unpublished', true);
        if ($limitToPublished) {
            $query->whereIn('status', ['published', null]);
        }

        collect(Arr::wrap($this->config('query_scope')))
            ->map(fn ($handle) => Scope::find($handle))
            ->filter()
            ->each(fn ($scope) => $scope->apply($query, []));

        return $query;
    }

    protected function getViewUrl($collection)
    {
        return User::current()->can('view', $collection->handle())
            ? $collection->showUrl()
            : null;
    }
}
