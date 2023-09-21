# laravel-nova-tabs
[![Latest Stable Version](https://poser.pugx.org/winglife/laravel-nova-tabs/v/stable)](https://packagist.org/packages/winglife/laravel-nova-tabs)
[![Total Downloads](https://poser.pugx.org/winglife/laravel-nova-tabs/downloads)](https://packagist.org/packages/winglife/laravel-nova-tabs)
[![Latest Unstable Version](https://poser.pugx.org/winglife/laravel-nova-tabs/v/unstable)](https://packagist.org/packages/winglife/laravel-nova-tabs)
[![License](https://poser.pugx.org/winglife/laravel-nova-tabs/license)](https://packagist.org/packages/winglife/laravel-nova-tabs)


## Requirements

- `php: ^7.4 | ^8`
- `laravel/nova: ^4`

## Installation

You can install the package in to a Laravel app that uses [Nova](https://nova.laravel.com) via composer:

```bash
composer require winglife/laravel-nova-tabs
```

## Usage

```php
// app/Nova/User.php

namespace App\Nova;

use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Winglife\LaravelNovaTabs\Tab;
use Winglife\LaravelNovaTabs\Tabs;

class User extends Resource
{
  
    public function fields(NovaRequest $request)
    {
        return [
            Tabs::make('Test', [
                Tab::make('One', [
                    ID::make()->sortable(),
                    Gravatar::make()->maxWidth(50),
                    Text::make('Email')
                        ->sortable()
                        ->rules('required', 'email', 'max:254')
                        ->creationRules('unique:users,email')
                        ->updateRules('unique:users,email,{{resourceId}}'),
                ]),
                Tab::make('Two', [
                    Text::make('Name')
                        ->sortable()
                        ->rules('required', 'max:255'),
                    Text::make('Email')
                        ->sortable()
                        ->rules('required', 'email', 'max:254')
                        ->creationRules('unique:users,email')
                        ->updateRules('unique:users,email,{{resourceId}}'),
                ]),
                Tab::make('Tree', [
                    Password::make('Password')
                        ->onlyOnForms()
                        ->creationRules('required', Rules\Password::defaults())
                        ->updateRules('nullable', Rules\Password::defaults()),
                ]),
            ]),
        ];
    }
```
