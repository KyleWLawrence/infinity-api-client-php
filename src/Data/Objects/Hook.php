<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Data\Objects;

class Hook extends ObjectBase
{
    protected array $events;

    protected string $url;

    protected ?int $user_id;

    protected ?string $secret;

    protected ?array $logs;

    protected array $required = ['url', 'events'];

    protected array $updateVars = ['url', 'events'];

    protected ?string $object = 'hook';

    public function getMostRecentLog()
    {
        $last = $this->logs;
        $last = end($last);

        return $last;
    }
}
