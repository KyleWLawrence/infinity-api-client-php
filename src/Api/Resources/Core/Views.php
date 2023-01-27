<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;

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
                'getAllLoop' => 'boards/{board_id}/views',
                'getAll' => 'boards/{board_id}/views',
                'get' => 'boards/{board_id}/views/{id}',
                'create' => 'boards/{board_id}/views',
                'update' => 'boards/{board_id}/views/{id}',
                'delete' => 'boards/{board_id}/views/{id}',
            ]
        );
    }
}
