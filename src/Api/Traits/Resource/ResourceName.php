<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Traits\Resource;

/**
 * Trait ResourceName
 **/
trait ResourceName
{
    /**
     * Appends the prefix to resource names
     *
     * @return string
     */
    protected function getResourceNameFromClass()
    {
        $resourceName = parent::getResourceNameFromClass();

        return $this->prefix.$resourceName;
    }
}
