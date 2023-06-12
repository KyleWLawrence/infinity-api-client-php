<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Resources\Core;

use KyleWLawrence\Infinity\Api\Resources\ResourceAbstract;
use KyleWLawrence\Infinity\Api\Traits\Resource\Defaults;
use KyleWLawrence\Infinity\Api\Traits\Resource\ProcessReturn;
use KyleWLawrence\Infinity\Api\Traits\Utility\InstantiatorTrait;

/**
 * The Items class exposes comment methods for items
 *
 * @method ItemValues values()
 * @method ItemComments comments()
 */
class Items extends ResourceAbstract
{
    use InstantiatorTrait;
    use Defaults {
        get as traitGet;
        getAll as traitGetAll;
        getAllLoop as traitGetAllLoop;
    }
    use ProcessReturn;

    public array $atts = [];

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources(): array
    {
        return [
            'values' => ItemValues::class,
            'comments' => ItemComments::class,
        ];
    }

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
                'getAll' => 'boards/{board_id}/items',
                'get' => 'boards/{board_id}/items/{id}',
                'create' => 'boards/{board_id}/items',
                'update' => 'boards/{board_id}/items/{id}',
                'delete' => 'boards/{board_id}/items/{id}',
            ]
        );
    }

    public function get(string $id, array $params = [])
    {
        if (! isset($params['expand[]']) || empty($params['expand[]'])) {
            $params['expand[]'] = 'values.attribute';
        }

        return $this->traitGet($id, $params);
    }

    public function getAll(array $params = [])
    {
        if (! isset($params['expand[]']) || empty($params['expand[]'])) {
            $params['expand[]'] = 'values.attribute';
        }

        return $this->traitGetAll($params);
    }

    public function getAllLoop(array $params = [])
    {
        if (! isset($params['expand[]']) || empty($params['expand[]'])) {
            $params['expand[]'] = 'values.attribute';
        }

        return $this->traitGetAllLoop($params);
    }

    public function setAttributes(null|object|array $atts): object
    {
        if (is_object($atts)) {
            $atts = $atts->toArray();
        } elseif (is_null($atts)) {
            return $this;
        }

        $atts = array_combine(array_column($atts, 'id'), $atts);
        $board_id = $this->getLatestChainedParameter([get_class()]);
        $bid = reset($board_id);

        $this->atts[$bid] = $atts;

        return $this;
    }
}
