<?php

namespace Infinity\Api\Resources\Core;

use Infinity\Api\Resources\ResourceAbstract;
use Infinity\Api\Traits\Utility\InstantiatorTrait;

/**
 * The Profile class exposes key methods for getting the current profile
 *
 * @method Profile profile()
 */
class Profile extends ResourceAbstract
{
    use InstantiatorTrait;

    /**
     * @var bool
     */
    protected bool $includeWorkspace = false;

    /**
     * {@inheritdoc}
     */
    public static function getValidSubResources(): array
    {
        return [
        ];
    }

    /**
     * Declares routes to be used by this resource.
     */
    protected function setUpRoutes(): void
    {
        parent::setUpRoutes();

        $this->setRoutes([
            'getCurrent' => 'profile',
        ]);
    }

    /**
     * Get the current profile
     *
     * @return null|\stdClass
     */
    public function getCurrent(): ?object
    {
        $route = $this->getRoute(__FUNCTION__);

        return $this->client->get(
            $route
        );
    }
}
