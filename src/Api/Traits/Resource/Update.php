<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Traits\Resource;

use KyleWLawrence\Infinity\Api\Exceptions\RouteException;

trait Update
{
    /**
     * Update a resource
     *
     * @param  int  $id
     * @param  array  $updateResourceFields
     * @param  string  $routeKey
     * @return null|\stdClass
     */
    public function update($id = null, array $updateResourceFields = [], $routeKey = __FUNCTION__)
    {
        $class = get_class($this);
        if (empty($id)) {
            $id = $this->getChainedParameter($class);
        }

        try {
            $route = $this->getRoute($routeKey, ['id' => $id]);
        } catch (RouteException $e) {
            if (! isset($this->resourceName)) {
                $this->resourceName = $this->getResourceNameFromClass();
            }

            $this->setRoute(__FUNCTION__, $this->resourceName.'/{id}');
            $route = $this->resourceName.'/'.$id.'';
        }

        $response = $this->client->put(
            $route,
            $updateResourceFields
        );

        return $this->processReturn( $response, 'obj' );
    }
}
