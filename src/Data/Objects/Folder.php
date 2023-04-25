<?php

namespace KyleWLawrence\Infinity\Data\Objects;

class Folder extends ObjectBase
{
    protected array $attribute_ids = [];

    protected ?string $parent_id = null;

    protected string $text;

    protected string $name;

    protected ?float $sort_order;

    protected object $settings;

    protected string $color;

    protected array $required = ['name'];

    protected array $update_vars = ['name', 'color', 'settings', 'sort_order', 'attribute_ids', 'parent_id'];

    protected string $object = 'folder';
}
