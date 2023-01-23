<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\Defaults;

/**
 * The Views class exposes comment methods for views
 */
class Views extends ResourceAbstract
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
                'getAll' => 'boards/{board_id}/views',
                'get' => 'boards/{board_id}/views/{id}',
                'create' => 'boards/{board_id}/views',
                'update' => 'boards/{board_id}/views/{id}',
                'delete' => 'boards/{board_id}/views/{id}',
            ]
        );
    }
}
