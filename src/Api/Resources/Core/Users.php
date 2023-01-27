<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\GetAll;
use KyleWLawrence\Infinity\Api\Traits\Resource\GetAllLoop;
use KyleWLawrence\Infinity\Api\Traits\Utility\InstantiatorTrait;

/**
 * The Users class exposes key methods for getting the current profile
 *
 * @method Users users()
 */
class Users extends ResourceAbstract
{
    use InstantiatorTrait;
    use GetAll;
    use GetAllLoop;

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
            'getAllLoop' => 'users',
            'getAll' => 'users',
        ]);
    }
}
