<?php

namespace Winglife\LaravelNovaTabs;

use Illuminate\Http\Resources\MergeValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Laravel\Nova\Contracts\ListableField;
use Laravel\Nova\Panel;
use Winglife\LaravelNovaTabs\Interfaces\TabInterface;

class Tabs extends Panel
{

    private string $preservedName;
    private $tabsCount;

    public function __construct($name, $fields = [])
    {
        $this->name = $name;
        $this->preservedName = $name;
        $this->withComponent('tabs');

        parent::__construct($name, $fields);
    }

    protected function prepareFields($fields)
    {
        $this->convertFieldsToTabs($fields)
            ->filter(static function (Tab $tab): bool {
                return $tab->shouldShow();
            })
            ->each(function (Tab $tab): void {
                $this->addFields($tab);
            });
        
        return $this->data ?? [];
    }

    public function addFields(TabInterface $tab): self
    {
        $this->tabs[] = $tab;

        foreach ($tab->getFields() as $field) {
            if ($field instanceof Panel) {
                $field->assignedPanel = $this;

                $this->addFields(
                    new Tab($field->name, $field->data)
                );
                continue;
            }

            if ($field instanceof MergeValue) {
                if (!isset($field->panel)) {
                    $field->assignedPanel = $this;
                    $field->panel = $this->name;
                }

                $this->addFields(
                    tap(new Tab($tab->getTitle(), $field->data), function (TabInterface $newTab) use ($tab): void {
                        if ($tab->getName() !== $tab->getTitle()) {
                            $newTab->name($tab->getName());
                        }
                    })
                );
                continue;
            }

            $field->panel = $this->name;
            $field->assignedPanel = $this;

            $meta = [
                'tab' => $tab->getName(),
                'tabSlug' => $tab->getSlug(),
                'tabPosition' => $tab->getPosition(),
                'tabInfo' => Arr::except($tab->toArray(), ['fields', 'slug'])
            ];

            if ($field instanceof ListableField) {
                $meta += [
                    'listable' => false,
                    'listableTab' => true,
                ];
            }

            $field->withMeta($meta);

            $this->data[] = $field;
        }

        return $this;
    }

    private function convertFieldsToTabs($fields): Collection
    {
        $fieldsCollection = collect(
            is_callable($fields) ? $fields() : $fields
        );

        return $fieldsCollection->map(function ($fields, $key) {
            return $this->convertToTab($fields, $key);
        })->values();
    }

    /**
     * @param  mixed  $fields
     * @param  string|int  $key
     * @return Tab
     */
    private function convertToTab($fields, $key): TabInterface
    {
        if ($fields instanceof TabInterface) {
            return $fields;
        }

        $this->tabsCount++;

        if ($fields instanceof Panel) {
            return new Tab($fields->name, $fields->data, $this->tabsCount);
        }

        if (!is_array($fields)) {
            return new Tab($fields->name, [$fields], $this->tabsCount);
        }

        return new Tab($key, $fields, $this->tabsCount);
    }


}
