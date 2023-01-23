<?php

namespace Infinity\Data\Traits;

use Exception;

trait GetData
{
    public function getObjById(string $id): object
    {
        $valMatch = array_search($id, array_column($this->data, 'id'));

        if (is_int($valMatch)) {
            return $this->data[$valMatch];
        } else {
            throw new Exception("Unable to find link \$value for $id from item #{$this->item_id}");
        }
    }
}
