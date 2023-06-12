<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects;

class Reference extends ObjectBase
{
    protected array $required = ['attribute_id', 'from_item_id', 'to_item_id'];

    protected array $update_vars = ['attribute_id', 'from_item_id', 'to_item_id'];

    protected string $attribute_id;

    protected string $from_item_id;

    protected string $to_item_id;

    protected ?string $object = 'reference';
}
