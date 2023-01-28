<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;

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
        $board_id = $this->getLatestChainedParameter([get_class()]);
        $boardParam = ['board_id' => $board_id];

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
