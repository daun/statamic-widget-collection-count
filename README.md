# Statamic Widget: Collection Count

Control panel widget for [Statamic](https://statamic.com/) that displays the
count of collection entries or taxonomy terms.

![Collection Count Widget Screenshot](./art/collection-count-widget.png)

## Installation

From your project root, run:

```sh
composer require daun/statamic-widget-collection-count
```

Alternatively, you can install the addon via the control panel.

## Usage

Add the widget to your control panel dashboard by adding it to the `widgets` array in the
`config/statamic/cp.php` config file. Pass in the names of collections to show.

```php
return [
    'widgets' => [
        [
            'type' => 'collection_count',
            'collections' => ['articles', 'categories', 'authors'],
        ]
    ]
];
```

## Options

### Display as individual cards

The widget displays counts in text sections with dividers between them. This works well if the widget
is used first in the dashboard. If you prefer a design that integrates more into the card layout
of other widgets, you can enable the `cards` config. This will render each count in a separate card.

```diff
return [
    'widgets' => [
        [
            'type' => 'collection_count',
            'collections' => ['articles', 'categories', 'authors'],
+           'cards' => true,
        ]
    ]
];
```

### Ignore draft entries

By default, all entries are counted, including drafts. Set the `ignore_unpublished` config value to
only count published entries.

```diff
return [
    'widgets' => [
        [
            'type' => 'collection_count',
            'collections' => ['articles', 'categories', 'authors'],
+           'ignore_unpublished' => true,
        ]
    ]
];
```

### Apply custom query scopes

Pass in the `query_scope` param to apply [custom scopes](https://statamic.dev/extending/query-scopes-and-filters) before counting.

```diff
return [
    'widgets' => [
        [
            'type' => 'collection_count',
            'collections' => ['articles', 'categories', 'authors'],
+           'query_scope' => 'unarchived',
        ]
    ]
];
```

### Usage with taxonomies

The widget can count taxonomy terms as well. Just use the taxonomy name instead.

```php
return [
    'widgets' => [
        [
            'type' => 'collection_count',
            'collections' => ['tags'],
        ]
    ]
];
```

## Requirements

Statamic 6 or higher. For Statamic 5 support, please use version 1.x of this addon.

## License

[MIT](https://opensource.org/licenses/MIT)
