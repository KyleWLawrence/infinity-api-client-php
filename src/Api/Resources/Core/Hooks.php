<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;
use KyleWLawrence\Infinity\Api\Traits\Resource\ProcessReturn;

/**
 * The Hooks class exposes comment methods for hooks
 */
class Hooks extends ResourceAbstract
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
                'getAll' => 'boards/{board_id}/hooks',
                'create' => 'boards/{board_id}/hooks',
                'update' => 'boards/{board_id}/hooks/{id}',
                'delete' => 'boards/{board_id}/hooks/{id}',
            ]
        );
    }
}
