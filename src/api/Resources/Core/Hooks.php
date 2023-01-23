<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\Defaults;

/**
 * The Hooks class exposes comment methods for hooks
 */
class Hooks extends ResourceAbstract
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
                'getAll' => 'boards/{board_id}/hooks',
                'create' => 'boards/{board_id}/hooks',
                'update' => 'boards/{board_id}/hooks/{id}',
                'delete' => 'boards/{board_id}/hooks/{id}',
            ]
        );
    }
}
