<?php

declare(strict_types=1);

namespace KyleWLawrence\Infinity\Api\Traits\Resource;

/**
 * This trait gives resources access to the default CRUD methods.
 */
trait Defaults
{
    use Get;
    use GetAll;
    use GetAllLoop;
    use Delete;
    use Create;
    use Update;
}
