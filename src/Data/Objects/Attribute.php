<?php

namespace KyleWLawrence\Infinity\Data\Objects;

use KyleWLawrence\Infinity\Data\Objects\Traits\Settings;

class Attribute extends ObjectBase
{
    use Settings;

    protected string|bool|array|null $default_data = null;

    protected string $name;

    protected object $settings;

    protected ?float $sort_order = 0.0;

    protected string $type;

    protected array $required = ['name', 'type'];

    protected array $update_vars = ['name', 'default_data', 'settings', 'type', 'sort_order'];

    protected ?string $object = 'attribute';

    protected array $folders;

    public array $folder_ids = [];

    public array $folder_names = [];

    public array $data_type = [
        'checkbox' => 'bool',
        'created_at' => 'string',
        'created_by' => 'int',
        'data' => 'string',
        'email' => 'string',
        'label' => 'array',
        'links' => 'array',
        'longtext' => 'string',
        'members' => 'array',
        'number' => 'float,int',
        'phone' => 'string',
        'progress' => 'int',
        'rating' => 'int',
        'source_folder' => 'string',
        'text' => 'string',
        'updated_at' => 'string',
        'vote' => 'int',
    ];
}
