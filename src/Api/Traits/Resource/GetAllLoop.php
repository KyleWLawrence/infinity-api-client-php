<?php

namespace KyleWLawrence\Infinity\Api\Traits\Resource;

use Exception;
use KyleWLawrence\Infinity\Api\Exceptions\RouteException;

trait GetAllLoop
{
    /**
     * List all of this resource
     *
     * @param  array  $params
     * @param  string  $routeKey
     * @return \stdClass | null
     *
     * @throws \Infinity\Api\Exceptions\AuthException
     * @throws \Infinity\Api\Exceptions\ApiResponseException
     */
    public function getAllLoop(array $params = [], $routeKey = 'getAll')
    {
        $params = array_merge($params,['limit' => 100]);

        try {
            $route = $this->getRoute($routeKey, $params);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $route = $this->resourceName;
            $this->setRoute(__FUNCTION__, $route);
        }

        $data = [];
        $has_more = true;
        $response = (object) [];

        while ($has_more) {
            $response = $this->client->get(
                $route,
                $params
            );

            $has_more = (isset($response->has_more)) ? $response->has_more : false;
            $params['after'] = $response->after;

            if (! isset($response->data)) {
                throw new Exception("Unable to find \$response->data for route $route");
            }

            $data = array_merge($data, $response->data);
        }

        $response->data = $data;
        
        return $this->processReturn( $response, 'list', 'data' );
    }
}
