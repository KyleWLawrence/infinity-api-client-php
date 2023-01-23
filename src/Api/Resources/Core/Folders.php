<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;

/**
 * The Folders class exposes comment methods for folders
 */
class Folders extends ResourceAbstract
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
                'getAll' => 'boards/{board_id}/folders',
                'get' => 'boards/{board_id}/folders/{id}',
                'create' => 'boards/{board_id}/folders',
                'update' => 'boards/{board_id}/folders/{id}',
                'delete' => 'boards/{board_id}/folders/{id}',
            ]
        );
    }
}
