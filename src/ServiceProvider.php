<?php

namespace Daun\CollectionCount;

use Daun\CollectionCount\Widgets\CollectionCount;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'daun';

    protected $widgets = [
        CollectionCount::class
    ];

    protected $vite = [
        'input' => [
            'resources/css/addon.css'
        ],
        'publicDirectory' => 'resources/dist',
    ];
}
