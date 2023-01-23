<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Resource\Defaults;

/**
 * The ItemComments class exposes comment methods for item comments
 */
class ItemComments extends ResourceAbstract
{
    use Defaults;

    /**
     * {@inheritdoc}
     */
    protected $objectName = 'comment';

    /**
     * {@inheritdoc}
     */
    protected $objectNamePlural = 'comments';

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
                'getAll' => 'boards/{board_id}/items/{item_id}/comments',
                'get' => 'boards/{board_id}/items/{item_id}/comments/{id}',
                'create' => 'boards/{board_id}/items/{item_id}/comments',
                'update' => 'boards/{board_id}/items/{item_id}/comments/{id}',
                'delete' => 'boards/{board_id}/items/{item_id}/comments/{id}',
            ]
        );
    }
}
