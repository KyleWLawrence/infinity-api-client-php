<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;
use KyleWLawrence\Infinity\Api\Traits\Resource\ProcessReturn;

/**
 * The ItemComments class exposes comment methods for item comments
 */
class ItemComments extends ResourceAbstract
{
    use Defaults;
    use ProcessReturn;

    /**
     * {@inheritdoc}
     */
    protected string $objectName = 'comment';

    /**
     * {@inheritdoc}
     */
    protected string $objectNamePlural = 'comments';

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
                'getAllLoop' => 'boards/{board_id}/items/{item_id}/comments',
                'getAll' => 'boards/{board_id}/items/{item_id}/comments',
                'get' => 'boards/{board_id}/items/{item_id}/comments/{id}',
                'create' => 'boards/{board_id}/items/{item_id}/comments',
                'update' => 'boards/{board_id}/items/{item_id}/comments/{id}',
                'delete' => 'boards/{board_id}/items/{item_id}/comments/{id}',
            ]
        );
    }
}
