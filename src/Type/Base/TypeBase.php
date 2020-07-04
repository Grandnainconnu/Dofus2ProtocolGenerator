<?php

namespace App\Type\Base;

interface TypeBase
{
    public function parseProtocol(array $parameters): void;
}