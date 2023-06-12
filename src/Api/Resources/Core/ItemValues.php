<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Delete;

/**
 * The ItemValues class exposes comment methods for item values
 */
class ItemValues extends ResourceAbstract
{
    use Delete;

    /**
     * {@inheritdoc}
     */
    protected string $objectName = 'value';

    /**
     * {@inheritdoc}
     */
    protected string $objectNamePlural = 'values';

    /**
     * {@inherticdoc}
     */
    public function getAdditionalRouteParams(): array
    {
        $board_id = $this->getChainedParameters();
        $item_id = $this->getLatestChainedParameter([get_class()]);

        $boardParam = [
            'board_id' => reset($board_id),
            'item_id' => reset($item_id),
        ];

        return array_merge($boardParam, $this->additionalRouteParams);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUpRoutes(): void
    {
        $this->setRoutes(
            [
                'delete' => 'boards/{board_id}/items/{item_id}/values/{id}',
            ]
        );
    }
}
