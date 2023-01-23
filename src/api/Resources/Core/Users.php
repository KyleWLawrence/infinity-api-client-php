<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\GetAll;
use Infinity\Api\Traits\Utility\InstantiatorTrait;

/**
 * The Users class exposes key methods for getting the current profile
 *
 * @method Users users()
 */
class Users extends ResourceAbstract
{
    use InstantiatorTrait;
    use GetAll;

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
            'getAll' => 'users',
        ]);
    }
}
