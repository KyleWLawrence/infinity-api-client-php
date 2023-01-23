<?php

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
    protected $objectName = 'value';

    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'values';

    /**
     * {@inherticdoc}
     */
    public function getAdditionalRouteParams(): array
    {
        $boardParam = [
            'board_id' => reset($this->getLatestChaiendParameter()),
            'item_id' => reset($this->getLatestChaiendParameter(['Infinity\Api\Resources\Core\Items'])),
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
