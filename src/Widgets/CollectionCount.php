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
        [$collections, $errors] = $this->getCollections();

        return view('daun::widgets.collection_count_widget', [
            'collections' => $collections,
            'errors' => $errors,
            'grid' => ! $this->config('width'),
            'card' => $this->config('card', true),
        ]);
    }

    protected function getCollections(): array
    {
        // If the config value is an integer, return that many dummy collections with random data
        if (is_int($count = $this->config('collections'))) {
            return [collect()->times($count, fn() => $this->dummyCollection()), collect()];
        }

        $collections = collect(Arr::wrap($this->config('collections', $this->config('collection', []))));

        $errors = $collections->filter()->count()
            ? $collections
                ->filter(fn($handle) => ! $this->collectionOrTaxonomyExists($handle))
                ->map(fn($handle) => "Collection [$handle] doesn't exist.")
            : collect('No collections specified');

        $result = $collections
            ->map(fn($handle) => $this->findCollectionOrTaxonomy($handle))
            ->filter(fn($collection) => $collection && $this->collectionOrTaxonomyShouldBeVisible($collection))
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

    protected function collectionOrTaxonomyShouldBeVisible(Collection|Taxonomy $collection): bool
    {
        return User::current()->can('view', $collection);
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

    protected function dummyCollection(): ?object
    {
        return (object) [
            'title' => $title = Arr::random(['Articles', 'Blog Posts', 'Events', 'Products', 'Projects', 'Recipes', 'Reviews', 'Tutorials']),
            'handle' => Str::slug($title),
            'url' => '/',
            'count' => (int) collect()->times(rand(1, 4))->map(fn() => random_int(1, 9))->join(''),
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
