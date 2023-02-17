<?php

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;
use KyleWLawrence\Infinity\Api\Traits\Resource\ProcessReturn;

/**
 * The Folders class exposes comment methods for folders
 */
class Folders extends ResourceAbstract
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
                'getAllLoop' => 'boards/{board_id}/folders',
                'getAll' => 'boards/{board_id}/folders',
                'get' => 'boards/{board_id}/folders/{id}',
                'create' => 'boards/{board_id}/folders',
                'update' => 'boards/{board_id}/folders/{id}',
                'delete' => 'boards/{board_id}/folders/{id}',
            ]
        );
    }
}
