<?php

namespace D3Creative\UtmBuilder;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'publicDirectory' => 'public',
        'buildDirectory' => 'build',
        'input' => ['resources/js/addon.js'],
    ];
}
