<?php

namespace KyleWLawrence\Infinity\Data\Objects;

class Board extends ObjectBase
{
    protected array $required = ['name'];

    protected array $update_vars = ['name', 'description', 'color', 'user_ids'];

    protected string $object = 'board';
}
