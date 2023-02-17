<?php

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
        if ($this->client->conv_objects === true) {
            $params = $this->getAdditionalRouteParams();
            $bid = (isset($params['board_id'])) ? $params['board_id'] : null;
            $atts = (isset($this->atts[$bid])) ? $this->atts[$bid] : null;

            if ($type === 'list') {
                $data->$key = conv_inf_list($data->$key, $bid, $atts);

                return $data;
            } else {
                return conv_inf_obj($data, $bid, $atts);
            }
        } else {
            return $data;
        }
    }
}
