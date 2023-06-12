<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;
use KyleWLawrence\Infinity\Api\Traits\Resource\ProcessReturn;

/**
 * The Attributes class exposes comment methods for attributes
 */
class Attributes extends ResourceAbstract
{
    use Defaults;
    use ProcessReturn;

    /**
     * {@inherticdoc}
     */
    public function getAdditionalRouteParams(): array
    {
        $board_id = $this->getLatestChainedParameter([get_class()]);
        $boardParam = ['board_id' => reset($board_id)];

        return array_merge($boardParam, $this->additionalRouteParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes(): void
    {
        $this->setRoutes(
            [
                'getAllLoop' => 'boards/{board_id}/attributes',
                'getAll' => 'boards/{board_id}/attributes',
                'get' => 'boards/{board_id}/attributes/{id}',
                'create' => 'boards/{board_id}/attributes',
                'update' => 'boards/{board_id}/attributes/{id}',
                'delete' => 'boards/{board_id}/attributes/{id}',
            ]
        );
    }
}
