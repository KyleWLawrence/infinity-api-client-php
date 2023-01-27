<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;

/**
 * The Items class exposes comment methods for items
 *
 * @method ItemValues values()
 * @method ItemComments comments()
 */
class Items extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources(): array
    {
        return [
            'values' => ItemValues::class,
            'comments' => ItemComments::class,
        ];
    }

    /**
     * {@inherticdoc}
     */
    public function getAdditionalRouteParams(): array
    {
        $latest = $this->getLatestChainedParameter();
        $boardParam = ['board_id' => reset($latest)];

        return array_merge($boardParam, $this->additionalRouteParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes(): void
    {
        $this->setRoutes(
            [
                'getAllLoop' => 'boards/{board_id}/items',
                'getAll' => 'boards/{board_id}/items',
                'get' => 'boards/{board_id}/items/{id}',
                'create' => 'boards/{board_id}/items',
                'update' => 'boards/{board_id}/items/{id}',
                'delete' => 'boards/{board_id}/items/{id}',
            ]
        );
    }
}
