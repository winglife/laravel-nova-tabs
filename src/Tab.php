<?php

namespace Winglife\LaravelNovaTabs;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;
use Winglife\LaravelNovaTabs\Interfaces\TabInterface;

class Tab implements TabInterface, \JsonSerializable, Arrayable
{

    /** @var string|Closure */
    protected $title;

    /** @var Field[] */
    protected $fields;

    protected $position;
    /**
     * @var bool|callable
     */
    private $isShow;
    /**
     * @var bool|callable
     */
    private $isHide;

    public function __construct($title, array $fields, $position = 0)
    {
        $this->title = $title;
        $this->fields = $fields;
        $this->position = $position;
    }

    public static function make($title, array $fields): self
    {
        return new static($title, $fields);
    }

    public function name(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function isShow($value)
    {
        if(is_bool($value) || is_callable($value)) {
            $this->isShow = $value;

            return $this;
        }

        throw new \Exception('The $condition parameter must be a boolean or a closure returning one');
    }

    public function isHide($value)
    {
        if (is_bool($value) || is_callable($value)) {
            $this->isHide = $value;

            return $this;
        }

        throw new \Exception('The $condition parameter must be a boolean or a closure returning one');
    }

    public function toArray(): array
    {
//        return [];
        return [
            'position' => $this->getPosition(),
            'title' => $this->getTitle(),
            'fields' => $this->getFields(),
            'name' => $this->getName(),
            'slug' => $this->getSlug(),
            'shouldShow' => $this->shouldShow(),
//            'bodyClass' => $this->getBodyClass(),
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * @return string|Closure
     */
    public function getTitle(): string|Closure
    {
        return $this->title;
    }

    private function resolve($value)
    {
        if ($value instanceof Closure) {
            return $value();
        }

        return $value;
    }

    public function getName(): string
    {
        return $this->name ?? $this->getTitle();
    }

    public function getSlug(): string
    {
        return Str::slug($this->getName());
    }

    public function shouldShow(): bool
    {
        if ($this->isShow !== null) {
            return $this->resolve($this->isShow);
        }

        if ($this->isHide !== null) {
            return !$this->resolve($this->isHide);
        }

        return true;
    }

    /**
     * @return array|Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return int|mixed
     */
    public function getPosition(): mixed
    {
        return $this->position;
    }

    /**
     * @return bool|callable
     */
    public function getIsShow(): callable|bool
    {
        return $this->isShow;
    }

    /**
     * @return bool|callable
     */
    public function getIsHide(): callable|bool
    {
        return $this->isHide;
    }



}
