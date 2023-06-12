<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects;

use KyleWLawrence\Infinity\Data\Objects\Traits\Settings;

class Folder extends ObjectBase
{
    use Settings;

    protected array $attribute_ids = [];

    protected ?string $parent_id = null;

    protected string $text;

    protected string $name;

    protected ?float $sort_order = 0.0;

    protected object $settings;

    protected ?string $color;

    protected array $required = ['name'];

    protected array $update_vars = ['name', 'color', 'settings', 'sort_order', 'attribute_ids', 'parent_id'];

    protected ?string $object = 'folder';
}
