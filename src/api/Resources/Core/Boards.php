<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\Create;
use Infinity\Api\Traits\Resource\Get;
use Infinity\Api\Traits\Resource\GetAll;
use Infinity\Api\Traits\Utility\InstantiatorTrait;

/**
 * The Boards class exposes key methods for reading and updating board data
 *
 * @method Attributes attributes()
 * @method Folders folders()
 * @method Hooks hooks()
 * @method Items items()
 * @method References references()
 * @method Views views()
 */
class Boards extends ResourceAbstract
{
    use InstantiatorTrait;
    use GetAll;
    use Get;
    use Create;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources(): array
    {
        return [
            'attributes' => Attributes::class,
            'folders' => Folders::class,
            'hooks' => Hooks::class,
            'items' => Items::class,
            'references' => References::class,
            'views' => Views::class,
        ];
    }

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes(): void
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'getAll' => 'boards',
            'get' => 'board/{id}',
            'create' => 'boards',
        ]);
    }
}
