<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\Defaults;

/**
 * The Attributes class exposes comment methods for attributes
 */
class Attributes extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inherticdoc}
     */
    public function getAdditionalRouteParams(): array
    {
        $boardParam = ['board_id' => reset($this->getLatestChaiendParameter())];

        return array_merge($boardParam, $this->additionalRouteParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes(): void
    {
        $this->setRoutes(
            [
                'getAll' => 'boards/{board_id}/attributes',
                'get' => 'boards/{board_id}/attributes/{id}',
                'create' => 'boards/{board_id}/attributes',
                'update' => 'boards/{board_id}/attributes/{id}',
                'delete' => 'boards/{board_id}/attributes/{id}',
            ]
        );
    }
}
