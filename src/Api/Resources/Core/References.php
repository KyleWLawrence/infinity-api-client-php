<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;

/**
 * The References class exposes comment methods for references
 */
class References extends ResourceAbstract
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
                'getAll' => 'boards/{board_id}/references',
                'get' => 'boards/{board_id}/references/{id}',
                'create' => 'boards/{board_id}/references',
                'delete' => 'boards/{board_id}/references/{id}',
            ]
        );
    }
}