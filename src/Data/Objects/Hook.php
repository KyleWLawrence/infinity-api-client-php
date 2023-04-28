<?php

namespace KyleWLawrence\Infinity\Data\Objects;

class Hook extends ObjectBase
{
    public array $events;

    public string $url;

    protected array $required = ['url', 'events'];

    protected array $updateVars = ['url', 'events'];

    protected ?string $object = 'hook';
}
