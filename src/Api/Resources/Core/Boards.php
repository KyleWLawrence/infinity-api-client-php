<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Create;
use KyleWLawrence\Infinity\Api\Traits\Resource\Get;
use KyleWLawrence\Infinity\Api\Traits\Resource\GetAll;
use KyleWLawrence\Infinity\Api\Traits\Resource\GetAllLoop;
use KyleWLawrence\Infinity\Api\Traits\Resource\ProcessReturn;
use KyleWLawrence\Infinity\Api\Traits\Utility\InstantiatorTrait;

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
    use GetAllLoop;
    use GetAll;
    use Get;
    use Create;
    use InstantiatorTrait;
    use ProcessReturn;

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
            'getAllLoop' => 'boards',
            'getAll' => 'boards',
            'get' => 'board/{id}',
            'create' => 'boards',
        ]);
    }
}
