<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Traits\Resource;

trait ProcessReturn
{
    /**
     * Get a specific ticket by id or series of ids
     *
     * @param    $id
     * @param  array  $queryParams
     * @param  string  $routeKey
     * @return null|\stdClass
     *
     * @throws MissingParametersException
     */
    public function processReturn(object $data, string $type = 'obj', string $key = 'data'): object|array
    {
        if ($this->client->conv_objects === true && $this->skipConvObj === false) {
            $params = $this->getAdditionalRouteParams();
            $bid = (isset($params['board_id'])) ? $params['board_id'] : null;
            $atts = (isset($this->atts[$bid])) ? $this->atts[$bid] : null;
            $item_id = (isset($params['item_id'])) ? $params['item_id'] : null;

            if ($type === 'list') {
                $data->$key = conv_inf_list($data->$key, $this->objectName, $bid, $atts);

                return $data;
            } else {
                return conv_inf_obj($data, $bid, $atts, $item_id);
            }
        } else {
            $this->skipConvObj = false;

            return $data;
        }
    }
}
