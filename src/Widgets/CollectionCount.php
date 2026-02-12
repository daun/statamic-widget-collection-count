<?php

namespace Daun\CollectionCount\Widgets;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection as IlluminateCollection;
use Illuminate\Support\Str;
use Statamic\Entries\Collection;
use Statamic\Facades\Collection as Collections;
use Statamic\Facades\Scope;
use Statamic\Facades\Taxonomy as Taxonomies;
use Statamic\Facades\User;
use Statamic\Contracts\Query\Builder;
use Statamic\Taxonomies\Taxonomy;
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
        [$collections, $errors] = $this->getCollections(
            collect(Arr::wrap($this->config('collections', $this->config('collection', []))))
        );

        return view('daun::widgets.collection_count', [
            'collections' => $collections,
            'errors' => $errors,
            'grid' => ! $this->config('width') || $collections->count() > 1,
            'card' => $this->config('card', true),
        ]);
    }

    protected function getCollections(IlluminateCollection $collections): array
    {
        $errors = $collections->filter()->count()
            ? $collections
                ->filter(fn($handle) => ! $this->collectionOrTaxonomyExists($handle))
                ->map(fn($handle) => "Collection [$handle] doesn't exist.")
            : collect('No collections specified');

        $result = $collections
            ->map(fn($handle) => $this->findCollectionOrTaxonomy($handle))
            ->filter()
            ->map(fn($collection) => $this->queryCollectionOrTaxonomy($collection));

        return [$result, $errors];
    }

    protected function findCollectionOrTaxonomy(string $handle): Collection|Taxonomy|null
    {
        if (Str::contains($handle, '::')) {
            [$type, $handle] = explode('::', $handle, 2);
        }

        return match ($type ?? 'collection') {
            'taxonomy' => Taxonomies::findByHandle($handle),
            default => Collections::findByHandle($handle) ?? Taxonomies::findByHandle($handle),
        };
    }

    protected function collectionOrTaxonomyExists(string $handle): bool
    {
        return (bool) $this->findCollectionOrTaxonomy($handle);
    }

    protected function queryCollectionOrTaxonomy(Collection|Taxonomy $collection): ?object
    {
        $query = $collection instanceof Taxonomy
            ? $collection->queryTerms()
            : $collection->queryEntries();
        $count = $this->applyQueryScopes($query)->count();
        $url = $this->getViewUrl($collection);

        return (object) [
            'title' => $collection->title(),
            'handle' => $collection->handle(),
            'url' => $url,
            'count' => $count
        ];
    }

    protected function applyQueryScopes(Builder $query)
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

    protected function getViewUrl(Collection|Taxonomy $collection)
    {
        return User::current()->can('view', $collection)
            ? $collection->showUrl()
            : null;
    }
}
