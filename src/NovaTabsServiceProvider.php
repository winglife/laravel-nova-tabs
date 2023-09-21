<?php

namespace Winglife\LaravelNovaTabs;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class NovaTabsServiceProvider extends ServiceProvider
{

    public function boot(): void {
        Nova::serving(function (ServingNova $event):void {
            Nova::script('tabs', __DIR__.'/../dist/js/field.js');
            Nova::style('tabs', __DIR__.'/../dist/css/field.css');
        });
    }


}
