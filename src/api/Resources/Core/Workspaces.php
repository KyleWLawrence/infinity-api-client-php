<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\GetAll;
use Infinity\Api\Traits\Utility\InstantiatorTrait;

/**
 * The Workspaces class exposes key methods for getting the current profile
 *
 * @method Workspaces workspaces()
 */
class Workspaces extends ResourceAbstract
{
    use InstantiatorTrait;
    use GetAll;

    /**
     * @var bool
     */
    protected bool $includeWorkspace = false;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources(): array
    {
        return [
        ];
    }

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes(): void
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'getAll' => 'workspaces',
        ]);
    }
}
