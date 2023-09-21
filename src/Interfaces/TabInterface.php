<?php

namespace Winglife\LaravelNovaTabs\Interfaces;

interface TabInterface
{

    public function name(string $name);

    public function isShow($value);


    public function isHide($value);


    public function toArray(): array;

}
