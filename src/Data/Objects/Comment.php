<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects;

class Comment extends ObjectBase
{
    protected ?string $parent_id = null;

    protected string $text;

    protected array $required = ['text'];

    protected array $update_vars = ['text', 'parent_id'];

    protected ?string $object = 'comment';
}
