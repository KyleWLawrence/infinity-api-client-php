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
        $params = array_merge(['limit' => 100], $params);

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
        $request = (object) [];

        while ($has_more) {
            $request = $this->client->get(
                $route,
                $params
            );

            $has_more = (isset($request->has_more)) ? $request->has_more : false;
            $params['after'] = $request->after;

            if (! isset($request->data)) {
                throw new Exception("Unable to find \$request->data for route $route");
            }

            $data = array_merge($data, $request->data);
        }

        $request->data = $data;

        return $request;
    }
}
