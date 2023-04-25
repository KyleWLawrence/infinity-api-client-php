<?php

namespace KyleWLawrence\Infinity\Data\Objects;

class View extends ObjectBase
{
    protected string $name;

    protected object $settings;

    protected string $folder_id;

    protected string $type;

    protected float $sort_order;

    protected ?string $parent_id = null;

    protected array $required = ['name', 'type', 'folder_id'];

    protected array $update_vars = ['name', 'folder_id', 'settings', 'type'];

    protected string $object = 'folderview';

    protected string $obj_name = 'view';

    protected string $obj_name_plural = 'views';
}
