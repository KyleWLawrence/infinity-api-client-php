<?php

namespace Infinity\Data\Lists;

use ArrayAccess;
use Exception;

class ListBase implements ArrayAccess
{
    protected array $list = [];

    public function __construct(
        array $apiObjects,
        public readonly string $board_id,
    ) {
        $this->setObjects($apiObjects);
    }

    protected function setObjects($apiObjects): void
    {
        foreach ($apiObjects as $obj) {
            if ($obj->deleted === true) {
                throw new Exception("Unexpected deleted item: $obj->id");
            }

            $this->list[] = conv_inf_obj($obj, $this->board_id);
        }
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->list[] = $value;
        } else {
            $this->list[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->list[$offset]);
    }

    public function offsetUnset($offset): void
    {
        unset($this->list[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->list[$offset]) ? $this->list[$offset] : null;
    }

    public function getById(string $id): object
    {
        $itemKey = array_search($id, array_column($this->list, 'id'));
        if (! is_int($itemKey)) {
            throw new Exception("Unable to find item id ($id) in list");
        }

        return $this->list[$itemKey];
    }
}
